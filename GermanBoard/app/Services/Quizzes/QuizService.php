<?php

namespace App\Services\Quizzes;

use App\Enum\QuizzesStatusEnum;
use App\Enum\StatusCodeEnum;
use App\Enum\TrainingTypeEnum;
use App\Models\Quiz;
use App\Models\TraineeTrainingQuiz;
use App\Repositories\Contracts\AnswerRepositoryInterface;
use App\Repositories\Contracts\QuestionRepositoryInterface;
use App\Repositories\Contracts\QuizRepositoryInterface;
use App\Repositories\Contracts\TraineeRepositoryInterface;
use App\Repositories\Contracts\TrainingRepositoryInterface;
use App\Repositories\Contracts\TrainingTraineeRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use function PHPUnit\Framework\returnArgument;

class QuizService
{
    public function __construct(
        private TrainingRepositoryInterface $trainingRepository,
        private QuizRepositoryInterface     $quizRepository,
        private QuestionRepositoryInterface $questionRepository,
        private AnswerRepositoryInterface   $answerRepository,
        private TrainingTraineeRepositoryInterface $trainingTraineeRepository,
    ) {}

    public function create(array $data): array
    {
        $user = auth()->user();
        if ($user->hasRole('trainee')) {
            throw new \DomainException('You are not allowed to create quizzes', StatusCodeEnum::UNAUTHORIZED->value);
        }

        $training = $this->trainingRepository->getById($data['training_id']);
        if ($training->type !== TrainingTypeEnum::RECORDED->value) {
            throw new \DomainException('You can only add quizzes for recorded trainings', StatusCodeEnum::FORBIDDEN->value);
        }

        $provider = $user->provider;

        if ($provider->id !== $training->provider_id) {
            throw new \DomainException('you can only add quizzes for your recorded trainings', StatusCodeEnum::UNAUTHORIZED->value);
        }

        $quiz_data = [
            'training_id' => $data['training_id'],
            'title' => $data['title'],
            'description' => $data['description'],
            'passing_score' => $data['passing_score'],
            'video_id' => $data['video_id'],
        ];
        $quiz = $this->quizRepository->store($quiz_data);

        collect($data['QA'])->each(function ($QA_Item) use ($quiz) {
            $question = $this->questionRepository->store([
                'quiz_id' => $quiz->id,
                'question_text' => $QA_Item['question'],
            ]);

            collect($QA_Item['answers'])->each(function ($answer_item) use ($question) {
                $this->answerRepository->store([
                    'question_id' => $question['id'],
                    'answer_text' => $answer_item['answer_text'],
                    'is_correct' => $answer_item['is_correct'],
                ]);
            });
        });

        return [];
    }

    public function getQuiz(int $quiz_id): Quiz
    {
        $quiz = $this->quizRepository->get($quiz_id);

        if (!$quiz) {
            throw new \DomainException("not found", StatusCodeEnum::NOT_FOUND->value);
        }

        if (auth()->user()->cannot('showQuiz', $quiz)) {
            throw new \DomainException("You are not authorized", StatusCodeEnum::UNAUTHORIZED->value);
        }

        return $this->quizRepository->get($quiz_id);
    }

    public function getQuizForTrainee(int $quiz_id): Quiz
    {
        $quiz = $this->quizRepository->get($quiz_id);

        if (!$quiz) {
            throw new \DomainException("not found", StatusCodeEnum::NOT_FOUND->value);
        }

        $trainingTrainee = $this->trainingTraineeRepository->checkIfUserEnrolledBefore(auth()->user()->trainee->id,$quiz->training_id);

        if (!$trainingTrainee) {
            throw new \DomainException("You are not enrolled in this training", StatusCodeEnum::UNAUTHORIZED->value);
        }


        return $quiz;
    }

    public function submitQuiz(array $data)
    {
        return DB::transaction(function () use ($data){
            $feedback = [];
            $questions_ids = collect($data)->map(function ($item) {
                return $item['question_id'];
            });

            $questionsWithAnswers = $this->questionRepository->showWithAnswers($questions_ids->toArray());

            $quiz = $questionsWithAnswers->first()?->quiz;
            $quizQuestionsLength = $quiz ? $quiz->questions->count() : 0;

            if ($quizQuestionsLength === 0) {
                return 0;
            }

            // Create a key-value array of correct answers [question_id => correct_answer_id]
            $correctAnswers = [];
            $questionsWithAnswers->each(function ($question) use (&$correctAnswers) {
                $correctAnswer = $question->answers->where('is_correct', true)->first();
                if ($correctAnswer) {
                    $correctAnswers[$question->id] = $correctAnswer->id;

                }
            });

            $eachQuestionScore = 100 / $quizQuestionsLength;
            $score = 0;

            collect($data)->each(function ($item) use (&$score, &$correctAnswers, $eachQuestionScore) {
                $questionId = $item['question_id'];
                $userAnswerId = $item['answer_id'];

                // Check if the question exists in correct answers and if user's answer is correct
                if (isset($correctAnswers[$questionId]) && $userAnswerId == $correctAnswers[$questionId]) {
                    $score += $eachQuestionScore;
                    // Remove the question from correct answers to prevent duplicate scoring
                    unset($correctAnswers[$questionId]);

                }
            });

            $trainee = auth()->user()->trainee;
            $training = $trainee->trainings()->where('trainings.id', $quiz->training_id)->first();
            $training_trainee = $this->trainingTraineeRepository->getByTrainingIdAndTraineeId($training->id ,$trainee->id);

            if(!$training_trainee){ throw  new \DomainException('you are not registered',StatusCodeEnum::BAD_REQUEST->value);}

            $traineeTrainingQuiz = TraineeTrainingQuiz::where('training_trainee_id' ,$training_trainee->id )
                ->where('quiz_id',$quiz->id)
                ->first();

            if($traineeTrainingQuiz){
                if($traineeTrainingQuiz->status == QuizzesStatusEnum::PASSED->value){
                    throw new \DomainException('already passed' , StatusCodeEnum::BAD_REQUEST->value);
                }
                elseif ($traineeTrainingQuiz->status == QuizzesStatusEnum::FAILED->value){
                    $traineeTrainingQuiz->update([
                        'score' => $score,
                        'status' => $score >= $quiz->passing_score ? QuizzesStatusEnum::PASSED->value : QuizzesStatusEnum::FAILED->value
                    ]);
                }
            }else{
                TraineeTrainingQuiz::create([
                    'quiz_id' => $quiz->id,
                    'training_trainee_id' => $training_trainee->id,
                    'score' => $score,
                    'status' => $score >= $quiz->passing_score ? QuizzesStatusEnum::PASSED->value : QuizzesStatusEnum::FAILED->value
                ]);
            }

            $result = $this->checkFinishedQuizzes($training,$training_trainee);

            if($result['all'] == $result['finished']){
                $training_trainee->update([
                    'passed_the_training' => true,
                    'achievement_percentage' => 100,
                ]);
            }else{
                $training_trainee->update([
                    'achievement_percentage' => (100 * $result['finished'])/$result['all'],
                ]);
            }

            // Prepare feedback
            // in this point the correctAnswers Array will contains only the wrong answers
            collect($data)->each(function ($item) use (&$correctAnswers, &$feedback) {
                $questionId = $item['question_id'];
                $userAnswerId = $item['answer_id'];

                if(isset($correctAnswers[$questionId])){
                    $feedback[] = [
                        'question_id' => $questionId,
                        'is_correct' => false
                    ];
                }else{
                    $feedback[] = [
                        'question_id' => $questionId,
                        'is_correct' => true
                    ];
                }

            });

            return [
                "score" => $score,
                "feedback"=>$feedback
            ];
        });

    }

    public function checkFinishedQuizzes($training,$training_trainee){
        $training_quizzes = $training->quizzes;
        $quizIds = $training_quizzes->map(function ($quiz){return $quiz->id;});

        $trainee_quizzes = $training_trainee->quizzes()
            ->where('status', QuizzesStatusEnum::PASSED->value)
            ->whereIn('quiz_id' , $quizIds)->get();

        return [
            'finished' =>sizeof($trainee_quizzes),
            'all' => sizeof($quizIds)
        ] ;

    }
}
