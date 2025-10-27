<?php

namespace App\Services\PaymentRequest;


use App\Enum\PaymentRequestStatusEnum;
use App\Enum\RolesEnum;
use App\Enum\StatusCodeEnum;
use App\Repositories\Contracts\PaymentRequestRepositoryInterface;
use App\Repositories\Contracts\ProviderRepositoryInterface;
use App\Repositories\Contracts\TrainingRepositoryInterface;
use Illuminate\Support\Facades\DB;

class PaymentRequestService
{


    public function __construct(
        private PaymentRequestRepositoryInterface $paymentRequestRepository,
        private TrainingRepositoryInterface $trainingRepository,
        private ProviderRepositoryInterface $providerRepository,

    ) {

    }

    public function create(array $data){

        $user =  auth()->user();

        if($user->hasRole(RolesEnum::ADMIN->value)){
            throw new \DomainException('unauthorized',StatusCodeEnum::UNAUTHORIZED->value);
        }

        $provider = $user->provider;

        if($provider->balance < $data['amount']){
            throw new \DomainException('You don\'t have enough balance to request this amount',StatusCodeEnum::BAD_REQUEST->value);
        }

        $data['provider_id'] = $provider->id;

        $this->paymentRequestRepository->create($data);
    }

    public function getMy(){
        $user = auth()->user();

        return $this->paymentRequestRepository->getByProviderId($user->provider->id);
    }

    public function getSummary()
    {
        $user = auth()->user();

        $provider = $user->provider;

        $totalGained = 0;

        $trainings = $this->trainingRepository->getByProvider($provider->id);

        $trainings->each(function ($training) use(&$response, &$totalGained){
            $totalGained += $training->total_income;
        });

        $totalGet = 0 ;

        $paymentRequests = $this->paymentRequestRepository->getByProviderId($user->provider->id);

        $paymentRequests->each(function ($paymentRequest) use(&$totalGet){
            if( $paymentRequest->status == PaymentRequestStatusEnum::APPROVED->value){
                if (is_numeric($paymentRequest->amount)) {
                    $totalGet += (float)$paymentRequest->amount;
                } else {
                    throw new \DomainException('detected string values in database', StatusCodeEnum::INTERNAL_SERVER_ERROR->value);
                }
            }
        });

        $final_balance = $provider->balance;

        if($totalGained - $totalGet != $final_balance){
            throw new \DomainException("There Is Something wrong in the calculation", StatusCodeEnum::INTERNAL_SERVER_ERROR->value);
        }

        return [
            'final_balance' => $final_balance,
            'total_gained' => $totalGained,
            'total_get' => $totalGet
        ];
    }

    public function all()
    {
        return $this->paymentRequestRepository->all();

    }

    public function approve($id)
    {
        DB::transaction(function () use($id){
            $paymentRequest = $this->paymentRequestRepository->find($id);

            if (!$paymentRequest) {
                throw new \DomainException('not found', StatusCodeEnum::NOT_FOUND->value);
            }

            if($paymentRequest->status == PaymentRequestStatusEnum::APPROVED->value){
                throw new \DomainException('already approved', StatusCodeEnum::BAD_REQUEST->value);
            }

            if($paymentRequest->amount > $paymentRequest->provider->balance ){
                throw new \DomainException('not enough balance', StatusCodeEnum::BAD_REQUEST->value);

            }

            $isSuccess = $this->paymentRequestRepository->approve($id);
            if ($isSuccess) {
                $isSuccessProviderUpdate = $paymentRequest->provider->update([
                    'balance' => $paymentRequest->provider->balance - $paymentRequest->amount
                ]);

                if (!$isSuccessProviderUpdate){
                    throw new \DomainException('Not Update Balance', StatusCodeEnum::INTERNAL_SERVER_ERROR->value);
                }
            }
        });

    }

    public function getAdminSummary()
    {

        $user = auth()->user();

        $adminProvider = $user->provider;

        $totalGained = 0;

        $myTrainings = $this->trainingRepository->getByProvider($adminProvider->id);

        $myTrainings->each(function ($training) use(&$totalGained){
            $totalGained += $training->total_income;
        });

        $totalProvidersGained= 0 ;

        $totalWithdrawalAmount = 0;

        $providerAmountInSystem = 0 ;

        $trainings = $this->trainingRepository->getAll();

        $trainings->each(function ($training) use(&$totalProvidersGained,$adminProvider){
            if($training->provider_id != $adminProvider->id) {
                $totalProvidersGained += $training->total_income;
            }
        });

        $paymentRequests = $this->paymentRequestRepository->all();
        $paymentRequests->each(function ($paymentRequest) use(&$totalWithdrawalAmount){
            if($paymentRequest->status == PaymentRequestStatusEnum::APPROVED->value) {
                $totalWithdrawalAmount += $paymentRequest->amount;
            }
        });

        $providers = $this->providerRepository->getAll();
        $providers->each(function ($provider) use(&$providerAmountInSystem,$adminProvider){
            if($provider->id != $adminProvider->id) {
                $providerAmountInSystem += $provider->balance;
            }
        });

        return [
            'total_trainings_gained' => $totalGained,
            'total_providers_gained' => $totalProvidersGained,
            'total_withdrawal'=> $totalWithdrawalAmount,
            'providers_amount_in_system' => $providerAmountInSystem
        ];
    }
}
