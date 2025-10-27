<?php

namespace App\Repositories\Contracts;

use App\Models\ComplaintAnswer;

interface AnswerComplaintRepositoryInterface
{
    public function answer(array $answer): ComplaintAnswer;
}
