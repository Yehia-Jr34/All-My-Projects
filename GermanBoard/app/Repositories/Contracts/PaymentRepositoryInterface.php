<?php

namespace App\Repositories\Contracts;

use App\Models\Payment;

interface PaymentRepositoryInterface
{
    public function store(array $data): Payment;

    public function update(int $trainee_id, string $status, string $payment_method): Payment;

    public function updateByPaymentIntentId(string $payment_intent_id, array $data): Payment;

    public function getById(int $id): Payment;

    public function getByClientSecret(string $client_secret): Payment;
}
