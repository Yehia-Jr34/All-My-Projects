<?php

namespace App\Services\QuizQuestion;

use App\Enum\StatusCodeEnum;
use App\Enum\TrainingTypeEnum;
use App\Repositories\Contracts\AnswerRepositoryInterface;
use App\Repositories\Contracts\QuestionRepositoryInterface;
use App\Repositories\Contracts\QuizRepositoryInterface;
use App\Repositories\Contracts\TrainingTraineeRepositoryInterface;
use Illuminate\Support\Facades\DB;

class QuizQuestion
{
    public function __construct(
        private QuizRepositoryInterface     $quizRepository,
        private QuestionRepositoryInterface $questionRepository,
        private AnswerRepositoryInterface   $answerRepository,
        private TrainingTraineeRepositoryInterface $trainingTraineeRepository,
    ) {}

    public function create(array $data): void
    {
        $quiz = $this->quizRepository->get($data['quiz_id']);

        if (auth()->user()->cannot('createQuizQuestion', $quiz)) {
            throw new \DomainException("You are not authorized", StatusCodeEnum::UNAUTHORIZED->value);
        }

        if ($quiz->training->type !== TrainingTypeEnum::RECORDED->value) {
            throw new \DomainException('You can only add quizzes for recorded trainings', StatusCodeEnum::FORBIDDEN->value);
        }

        $question = $this->questionRepository->store([
            'quiz_id' => $data['quiz_id'],
            'question_text' => $data['question']
        ]);

        $answers = collect($data['answers'])->map(function ($item) use ($question) {
            return [
                'question_id' => $question['id'],
                'created_at' => now(),
                ...$item
            ];
        });

        $this->answerRepository->insert($answers->toArray());
    }

    public function edit(array $data)
    {

        DB::transaction(function () use ($data) {
            $question = $this->questionRepository->show($data['question_id']);

            if (auth()->user()->cannot('editQuizQuestion', $question->quiz)) {
                throw new \DomainException("You are not authorized", StatusCodeEnum::UNAUTHORIZED->value);
            }

            if (isset($data['question']))
                $this->questionRepository->edit($question->id, [
                    'question_text' => $data['question']
                ]);

            $answers_ids = [];

            if (isset($data['answers'])) {
                foreach ($data['answers'] as $answer) {
                    $data = [];

                    if (array_key_exists('answer_text', $answer)) {
                        $data['answer_text'] = $answer['answer_text'];
                    }

                    if (array_key_exists('is_correct', $answer)) {
                        $data['is_correct'] = $answer['is_correct'];
                    }
                    if (!empty($data)) {
                        if (array_key_exists('id', $answer)) {
                            $this->answerRepository->edit($answer['id'], $data, $question->id);
                            $answers_ids[] = $answer['id'];
                        } else {
                            $this->answerRepository->store(array_merge($data, [
                                'question_id' => $question->id
                            ]));
                        }
                    }
                }
            }

            $deleted_answers = $question->answers->filter(function ($item) use ($answers_ids) {
                return !(in_array($item->id, $answers_ids));
            });

            $deleted_answers_id = $deleted_answers->map(fn($item) => $item->id);

            if (!empty($deleted_answers_id)) {
                $this->answerRepository->delete($deleted_answers_id->toArray());
            }
        });
    }

    public function checkAnswer(array $data)
    {
        $quiz = $this->quizRepository->get($data['quiz_id']);

        if (!$quiz) {
            throw new \DomainException("Quiz not found", StatusCodeEnum::NOT_FOUND->value);
        }

        $question = $this->questionRepository->show($data['question_id']);
        $answer = $this->answerRepository->show($data['answer_id']);

        if (!$question || !$answer) {
            throw new \DomainException("Question or answer not found", StatusCodeEnum::NOT_FOUND->value);
        }

        if ($question->quiz_id !== $quiz->id) {
            throw new \DomainException("Question does not belong to the quiz", StatusCodeEnum::FORBIDDEN->value);
        }

        if ($answer->question_id !== $question->id) {
            throw new \DomainException("Answer does not belong to the question", StatusCodeEnum::FORBIDDEN->value);
        }

        $trainingTrainee = $this->trainingTraineeRepository->checkIfUserEnrolledBefore(auth()->user()->trainee->id,$quiz->training_id);

        if (!$trainingTrainee) {
            throw new \DomainException("You are not enrolled in this training", StatusCodeEnum::UNAUTHORIZED->value);
        }

        if ($answer->is_correct) {
            return true;
        }

        return false;
    }
}
