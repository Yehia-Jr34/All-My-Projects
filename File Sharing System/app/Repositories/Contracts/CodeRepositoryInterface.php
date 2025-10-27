<?php

namespace App\Repositories\Contracts;

use App\DTOs\Code\CodeDTO;

interface CodeRepositoryInterface
{
    public function storeCode(int $code, int $user_id);

    public function getCodeByUserId(int $user_id) : ?CodeDTO;

    public function deleteAllUserCodes (int $user_id ) : void;

}
