<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TraineeTrainingQuiz extends Model
{
    protected $fillable = [
        'quiz_id',
        'training_trainee_id',
        'score',
        'status',
    ];
}
