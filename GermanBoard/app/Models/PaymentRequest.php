<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentRequest extends Model
{
    protected $fillable = [
        'status',
        'amount',
        'account_number',
        'provider_id',
        'confirmed_at'
    ];

    public function provider():BelongsTo{
        return $this->belongsTo(Provider::class);
    }
}
