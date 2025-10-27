<?php

namespace App\Policies;

use App\Models\Quiz;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class QuizPolicy
{
    public function createQuizQuestion(User $user, Quiz $quiz){
        return $user?->provider->id === $quiz->training->provider->id;
    }

    public function editQuizQuestion(User $user, Quiz $quiz){
        return $user?->provider->id === $quiz->training->provider->id;
    }

    public function showQuiz(User $user, Quiz $quiz){
        return $user?->provider->id === $quiz->training->provider->id;
    }
}
