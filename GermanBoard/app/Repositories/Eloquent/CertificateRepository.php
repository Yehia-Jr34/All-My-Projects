<?php

namespace App\Repositories\Eloquent;

use App\Models\Certificate;
use App\Repositories\Contracts\CertificateRepositoryInterface;

class CertificateRepository implements CertificateRepositoryInterface
{

    public function create(array $data): Certificate
    {
        return Certificate::create($data);
    }

    public function getCertificateByCode($code): ?Certificate
    {
        return Certificate::where('certification_code' , $code)->first();
    }

    public function delete($id): void
    {
        Certificate::find($id)->delete();
    }

    public function getById($id): ?Certificate
    {
        return Certificate::find($id);
    }

    public function index(?string $certification_code)
    {
        if($certification_code)
           return Certificate::with(["training_trainee.training.provider",'training_trainee.trainee'])
               ->where('certification_code','like',"{$certification_code}%")
               ->orderBy('certification_attached_at', 'desc')
               ->paginate(5);

        return Certificate::with(["training_trainee.training.provider",'training_trainee.trainee'])
            ->orderBy('certification_attached_at', 'desc')
            ->paginate(5);
    }
}
