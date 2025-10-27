<?php
declare(strict_types=1);

namespace App\DTOs\Code;


use App\Models\Code;


class CodeDTO {
    public function __construct(
        public readonly int $id,
        public readonly string $code,
        public readonly string $expired_at,
    ) {}

    public static function fromCode (?Code $code ) : ?CodeDTO{

        if($code === null){
            return null;
        }

        return new self(
            $code->id,
            $code->code,
            $code->expired_at,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'expired_at' => $this->expired_at,
        ];
    }
}
