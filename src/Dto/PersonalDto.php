<?php

namespace App\Dto;

use App\Interface\DtoInterface;

readonly class PersonalDto implements DtoInterface
{
    public function __construct(
        public string $name,
        public string $phone,
        public string $email,
    ) {}
}
