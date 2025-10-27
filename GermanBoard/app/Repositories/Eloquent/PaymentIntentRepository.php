<?php

namespace App\Repositories\Eloquent;

use App\Models\PaymentIntent;
use App\Repositories\Contracts\PaymentIntentRepositoryInterface;

class PaymentIntentRepository implements PaymentIntentRepositoryInterface
{
    public function store(array $data): PaymentIntent
    {
        return PaymentIntent::create($data);
    }

    public function getById(int $id): PaymentIntent
    {
        return PaymentIntent::find($id);
    }
}
