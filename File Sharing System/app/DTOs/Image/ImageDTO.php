<?php

namespace App\DTOs\Image;

class ImageDTO
{
    public function __construct(
        public readonly ?string $path
    )
    {
    }

    public static function create(?string $path) : self
    {
        return new self (
            path: $path
        );
    }
}
