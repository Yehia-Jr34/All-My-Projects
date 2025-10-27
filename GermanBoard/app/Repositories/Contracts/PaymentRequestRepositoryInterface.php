<?php

namespace App\Repositories\Contracts;


use App\Models\PaymentRequest;
use Illuminate\Support\Collection;

interface PaymentRequestRepositoryInterface
{
    public function create(array $data);

    public function getByProviderId($provider_id):Collection;

    public function all():Collection;

    public function approve($id):bool;

    public function find($id):PaymentRequest | null;


}
