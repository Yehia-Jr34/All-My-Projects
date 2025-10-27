<?php

namespace App\DTOs\Auth;

class UserVerifyDTO
{
    public function __construct(
        public readonly string $email,
        public readonly string $code,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            $data['email'],
            $data['code'],
        );
    }
}
