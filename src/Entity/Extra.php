<?php

namespace App\Entity;

readonly class Extra extends AbstractEntity
{
    public function __construct(
        public string $informations,
    ) {}
}