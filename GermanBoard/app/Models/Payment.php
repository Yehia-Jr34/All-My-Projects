<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Payment extends Model
{
    protected $fillable = [
        'payment_intent_id',
        'amount',
        'currency',
        'status',
        'payment_method',
        'paid_at',
        'training_trainee_id',
    ];

    public function trainee(): BelongsTo
    {
        return $this->belongsTo(Trainee::class);
    }

    public function training(): BelongsTo
    {
        return $this->belongsTo(Training::class);
    }

    public function payment_intent(): HasOne
    {
        return $this->hasOne(PaymentIntent::class);
    }
}
