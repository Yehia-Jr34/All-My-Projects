<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberShip extends Model
{
    protected $table = 'memberships';
    protected $fillable = [
        'provider_id',
        'start_at',
        'expired_at',
        'duration',
        'remind_at',
        'is_revoked',
    ];

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }
}
