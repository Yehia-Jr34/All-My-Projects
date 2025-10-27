<?php

namespace App\Http\Resources\Balance;

use App\Enum\ProvidersMediaEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentRequestAdminAllResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'amount'=> $this->amount,
            'status' =>$this->status,
            'created_at' =>$this->created_at,
            'account_number' => $this->account_number,
            'confirmed_at' =>$this->confirmed_at,
            'provider' => $this->whenLoaded('provider', function () {
                return [
                    'id' => $this->provider->id,
                    'first_name' => $this->provider->first_name,
                    'last_name' => $this->provider->last_name,
                    'balance' => $this->provider->balance,
                    'photo' => $this->provider->getFirstMediaUrl(ProvidersMediaEnum::PHOTO->value),
                    'user'=> [
                        'id' => $this->provider->user->id,
                        'email'=> $this->provider->user->email,
                    ]
                ];
            })
        ];
    }
}
