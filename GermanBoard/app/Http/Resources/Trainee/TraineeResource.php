<?php

namespace App\Http\Resources\Trainee;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TraineeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $trainee = $this->whenLoaded('trainee');

        return [
            'trainee_id' => $trainee->id,
            'first_name' => $trainee->first_name,
            'last_name' => $trainee->last_name,
            'phone_number' => $trainee->phone_number,
            'date_of_birth' => $trainee->date_of_birth,
            'gender' => $trainee->gender,
            'country' => $trainee->country,
            'address' => $trainee->address,
            'email' => $trainee->user->email,
            'training_trainee_id' => $this->id,
            'passed_the_training' => $this->passed_the_training,
            'payment_status' => $this->payment_status
        ];
    }
}
