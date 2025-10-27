<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendedSession extends Model
{
    protected $fillable = [
        'session_id',
        'training_trainee_id',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(TrainingSession::class);
    }

    public function training_trainee(): BelongsTo
    {
        return $this->belongsTo(TrainingTrainee::class);
    }
}
