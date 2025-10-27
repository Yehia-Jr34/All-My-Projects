<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Trainee extends Model
{
    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'phone_number',
        'date_of_birth',
        'gender',
        'country',
        'address',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function training_trainees(): HasMany
    {
        return $this->hasMany(TrainingTrainee::class);
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    public function trainee_videos(): HasMany
    {
        return $this->hasMany(TraineeVideo::class);
    }

    public function trainings()
    {
        return $this->belongsToMany(Training::class, 'training_trainees')
            ->using(TrainingTrainee::class)
            ->withPivot([
                'remaining_hours',
                'passed_the_training',
                'payment_status',
                'achievement_percentage'
            ])
            ->withTimestamps();
    }

    public function complaints(): HasMany
    {
        return $this->hasMany(Complaint::class);
    }
}
