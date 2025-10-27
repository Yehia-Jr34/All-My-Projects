<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\Pivot;

class TrainingTrainee extends Pivot
{
    protected $table = 'training_trainees';

    public $incrementing = true;

    protected $fillable = [
        'training_id',
        'trainee_id',
        'passed_the_training',
        'remaining_hours',
        'achievement_percentage',
        'payment_status'
    ];

    public function trainee(): BelongsTo
    {
        return $this->belongsTo(Trainee::class);
    }

    public function training(): BelongsTo
    {
        return $this->belongsTo(Training::class);
    }

    public function quizzes(): HasMany
    {
        return $this->hasMany(TraineeTrainingQuiz::class, 'training_trainee_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'training_trainee_id');
    }

    public function certificate(): HasOne
    {
        return $this->hasOne(Certificate::class, 'training_trainee_id');
    }
}
