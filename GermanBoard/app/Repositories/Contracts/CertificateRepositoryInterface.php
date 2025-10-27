<?php

namespace App\Repositories\Contracts;

use App\Models\Certificate;

interface CertificateRepositoryInterface
{
    public function create(array $data): Certificate;

    public function getCertificateByCode($code) : ?Certificate;

    public function delete($id):void;

    public function getById($id):?Certificate;

    public function index(?string $certification_code);
}
