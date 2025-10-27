<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ProviderInternalTrainer extends Pivot
{
    protected $fillable = [
        'provider_id',
        'internal_trainer_id',
    ];
}
