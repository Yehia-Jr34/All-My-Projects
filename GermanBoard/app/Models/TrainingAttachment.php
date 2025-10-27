<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrainingAttachment extends Model
{
    protected $fillable = [
        'training_id',
        'description',
        'src',
    ];

    public function training(): BelongsTo
    {
        return $this->belongsTo(Training::class);
    }
}
