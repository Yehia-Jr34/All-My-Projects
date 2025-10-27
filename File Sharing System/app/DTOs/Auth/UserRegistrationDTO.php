<?php

namespace App\DTOs\Auth;

use Illuminate\Http\File;
use Illuminate\Http\UploadedFile;

class UserRegistrationDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $password,
        public readonly string $username,
        public readonly UploadedFile $image
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            $data['name'],
            $data['email'],
            bcrypt($data['password']), // Hash the password here
            $data['username'],
            $data['image'],
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'username'=>$this->username,
            'password' => $this->password,
            'image' => $this->image
        ];
    }
}
