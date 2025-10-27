<?php

namespace App\Repositories\Eloquent;

use App\Models\Payment;
use App\Repositories\Contracts\PaymentRepositoryInterface;

class PaymentRepository implements PaymentRepositoryInterface
{
    public function store(array $data): Payment
    {
        return Payment::create($data);
    }

    public function update(int $trainee_id, string $status, string $payment_method): Payment
    {
        $payment = Payment::where('trainee_id', $trainee_id)
            ->orderByDesc('created_at')
            ->first();
        $payment->status = $status;
        $payment->payment_method = $payment_method;
        $payment->save();

        return $payment;
    }

    public function getById(int $id): Payment
    {
        return Payment::find($id);
    }

    public function updateByPaymentIntentId(string $payment_intent_id, array $data): Payment
    {
        $payment = Payment::where('payment_intent_id', $payment_intent_id)
            ->first();

        $payment->update($data);

        return $payment;
    }

    public function getByClientSecret(string $client_secret): Payment
    {
        return Payment::select('id', 'status')
            ->where('payment_intent_id', $client_secret)
            ->first();
    }
}
