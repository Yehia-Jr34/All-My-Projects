<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KeyLearningObjective extends Model
{
    public $fillable = [
        'text_ar',
        'text_en',
        'text_du',
        'training_id',
    ];

    public function training(): BelongsTo
    {
        return $this->belongsTo(Training::class);
    }
}
