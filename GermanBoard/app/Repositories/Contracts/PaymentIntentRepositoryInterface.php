<?php

namespace App\Repositories\Contracts;

use App\Models\PaymentIntent;

interface PaymentIntentRepositoryInterface
{
    public function store(array $data): PaymentIntent;

    public function getById(int $id): PaymentIntent;
}
