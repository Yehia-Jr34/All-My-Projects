<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrainingTag extends Model
{
    public function tags(): BelongsTo
    {
        return $this->belongsTo(Tag::class, 'tag_id');
    }

    public function training(): BelongsTo
    {
        return $this->belongsTo(Training::class);
    }
}
