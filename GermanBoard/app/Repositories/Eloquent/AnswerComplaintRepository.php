<?php

namespace App\Repositories\Eloquent;

use App\Models\ComplaintAnswer;
use App\Repositories\Contracts\AnswerComplaintRepositoryInterface;

class AnswerComplaintRepository implements AnswerComplaintRepositoryInterface
{

    public function answer(array $answer): ComplaintAnswer
    {
        return ComplaintAnswer::create($answer);
    }
}
