<?php

namespace App\Dto;

readonly class PersonalDto
{
    public function __construct(
        public string $name,
        public string $phone,
        public string $email,
    ) {}
}
