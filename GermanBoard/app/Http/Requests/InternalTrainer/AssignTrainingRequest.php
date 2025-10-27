<?php

namespace App\Http\Requests\InternalTrainer;

use App\Http\Requests\BaseRequest;

class AssignTrainingRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true; // Allow request, adjust if needed
    }

    public function rules(): array
    {
        return [
            'internal_trainer_id'=> 'required|numeric|exists:internal_trainers,id',
            'training_id'      => 'required|numeric|exists:trainings,id',
        ];
    }
}
