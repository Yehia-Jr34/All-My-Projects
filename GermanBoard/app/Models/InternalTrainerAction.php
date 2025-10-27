<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InternalTrainerAction extends Model
{
    protected $fillable = [
        'training_id',
        'internal_trainer_id',
        'action',
    ];

    public function training():BelongsTo{
        return $this->belongsTo(Training::class);
    }
}
