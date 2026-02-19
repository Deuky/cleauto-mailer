<?php

namespace App\Entity;

readonly class Personal
{
    public function __construct(
        public string $name,
        public string $phone,
        public string $email,
    ) {}
}