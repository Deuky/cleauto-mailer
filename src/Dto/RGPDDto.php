<?php

namespace App\Dto;

use DateTimeInterface;

use App\Interface\DtoInterface;

readonly class RGPDDto implements DtoInterface
{
    public function __construct(
        public bool $status,
        public string $content,
        public DateTimeInterface $requestDate
    ) {}
}
