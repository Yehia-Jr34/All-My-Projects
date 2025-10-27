<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Certificate extends Model {

    protected $fillable = [
        'certification_image',
        'training_trainee_id',
        'certification_code',
        'certification_attached_at'
    ];

    public function training_trainee(): BelongsTo
    {
        return $this->belongsTo(TrainingTrainee::class);
    }
}
