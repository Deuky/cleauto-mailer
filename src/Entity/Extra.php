<?php

namespace App\Entity;

readonly class Extra
{
    public function __construct(
        public string $informations,
    ) {}
}