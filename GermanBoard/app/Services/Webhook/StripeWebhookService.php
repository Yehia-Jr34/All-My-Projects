<?php

namespace App\Services\Webhook;

use App\Enum\NotificationTypesEnum;
use App\Jobs\SendNotification;
use App\Models\Notification;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use App\Repositories\Contracts\TrainingRepositoryInterface;
use App\Repositories\Contracts\TrainingTraineeRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\Webhook;

class StripeWebhookService
{
    public function __construct(
        private PaymentRepositoryInterface $paymentRepository,
        private TrainingTraineeRepositoryInterface $trainingTraineeRepository,
    ) {}

    public function handleWebhook(Request $request): void
    {


        DB::transaction(function () use($request){
            $payload = $request->getContent();
            $sigHeader = $request->header('Stripe-Signature');
            $endpointSecret = env('STRIPE_WEBHOOK_SECRET');

            try {
                $event = Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
            } catch (\Exception $e) {
                throw new \DomainException('Invalid webhook signature ' . $e->getMessage(), $e->getCode());
            }


            switch ($event->type) {
                case 'payment_intent.succeeded':

                    $paymentIntent = $event->data->object;

                    Log::info('payment intent: ' . $paymentIntent);
                    Log::info('Payment Succeeded: ' . $paymentIntent->id);

                    $payment = $this->paymentRepository->updateByPaymentIntentId($paymentIntent->client_secret, [
                        'status' => 'success',
                        'paid_at' => now(),
                        'amount' => $paymentIntent->amount / 100,
                        'currency' => $paymentIntent->currency,
                        'payment_method' => $paymentIntent->payment_method,
                    ]);

                    Log::info('payment : ' . $payment);

                    SendNotification::dispatch(
                        [$paymentIntent->metadata->user_id],
                        'Payment Succeeded',
                        'Your payment has been successfully processed.',
                        NotificationTypesEnum::PAYMENT_SUCCEEDED,
                        null,
                    );

                    SendNotification::dispatch(
                        [$paymentIntent->metadata->user_id],
                        'Enrolled in training',
                        'You have been enrolled in ' . $paymentIntent->metadata->training_name . ' training, Happy Learning!',
                        NotificationTypesEnum::ENROLLED_IN_TRAINING,
                        null,
                    );

                    $this->trainingTraineeRepository->update($paymentIntent->metadata->registration_id, [
                        'payment_status' => 'success',
                    ]);

                    $training =  $this->trainingTraineeRepository->getById($paymentIntent->metadata->registration_id)->training;

                    $training->update([
                        'total_income' => $training->total_income + $training->price,
                    ]);

                    $user_id = $training->provider->user->id;
                    Notification::create([
                        'title' => 'New Registration',
                        'body' => "You Have Trainee Registered in $training->title_en training",
                        'notification_type' => NotificationTypesEnum::NEW_REGISTRATION->value,
                        'user_id' => $user_id
                    ]);

                    $training->provider->update([
                        'balance' => $training->price + $training->provider->balance
                    ]);

                    break;

                case 'payment_intent.payment_failed':
                    $paymentIntent = $event->data->object;
                    Log::warning('Payment Failed: ' . $paymentIntent->id);
                    $this->paymentRepository->updateByPaymentIntentId($paymentIntent->client_secret, [
                        'status' => 'failed',
                    ]);

                    $declineCode = $paymentIntent->last_payment_error->decline_code ?? null;

                    if ($declineCode === 'insufficient_funds') {
                        SendNotification::dispatch(
                            [$paymentIntent->metadata->user_id],
                            'Payment Failed',
                            'Your payment has been failed due to insufficient funds, please try again.',
                            NotificationTypesEnum::PAYMENT_FAILED,
                            null,
                        );
                    } else if ($declineCode === 'card_declined') {
                        SendNotification::dispatch(
                            [$paymentIntent->metadata->user_id],
                            'Payment Failed',
                            'Your payment has been failed due to card declined, please try again.',
                            NotificationTypesEnum::PAYMENT_FAILED,
                            null,
                        );
                    } else {
                        SendNotification::dispatch(
                            [$paymentIntent->metadata->user_id],
                            'Payment Failed',
                            'Your payment has been failed, please try again.',
                            NotificationTypesEnum::PAYMENT_FAILED,
                            null,
                        );
                    }

                    $this->trainingTraineeRepository->update($paymentIntent->metadata->registration_id, [
                        'payment_status' => 'failed',
                    ]);

                    break;
            }

        });

    }
}
