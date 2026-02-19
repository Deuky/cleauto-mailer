<?php

namespace App\Dto;

use DateTimeInterface;

readonly class RgpdDto
{
    public function __construct(
        public bool $status,
        public string $content,
        public DateTimeInterface $requestDate
    ) {}
}
