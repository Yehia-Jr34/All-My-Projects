<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TrainingSession extends Model
{
    protected $fillable = [
        'training_id',
        'start_date',
        'title',
        'status',
        'upcoming_notification_sent',
        'meet_url'
    ];

    public function training(): BelongsTo
    {
        return $this->belongsTo(Training::class);
    }

    public function attended_sessions(): HasMany
    {
        return $this->hasMany(AttendedSession::class);
    }
}
