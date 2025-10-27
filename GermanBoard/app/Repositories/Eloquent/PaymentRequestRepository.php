<?php

namespace App\Repositories\Eloquent;


use App\Enum\PaymentRequestStatusEnum;
use App\Models\PaymentRequest;
use App\Repositories\Contracts\PaymentRequestRepositoryInterface;
use Illuminate\Support\Collection;

class PaymentRequestRepository implements PaymentRequestRepositoryInterface
{

    public function create(array $data)
    {
       PaymentRequest::create($data);
    }

    public function getByProviderId($provider_id): Collection
    {
       return PaymentRequest::where('provider_id' ,$provider_id)->orderBy('status')->get();
    }

    public function all(): Collection
    {
        return PaymentRequest::with('provider')
            ->orderBy('status')
            ->get();
    }

    public function approve($id) : bool
    {
        return PaymentRequest::find($id)->update([
            'status'=>PaymentRequestStatusEnum::APPROVED->value,
            'confirmed_at' => now()
        ]);
    }

    public function find($id):PaymentRequest | null
    {
        return PaymentRequest::with('provider')->find($id);
    }
}
