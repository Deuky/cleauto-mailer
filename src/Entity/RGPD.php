<?php

namespace App\Entity;

use DateTimeInterface;

readonly class RGPD
{
    public function __construct(
        public bool $status,
        public string $content,
        public DateTimeInterface $requestDate,
        public DateTimeInterface $requestTraitDate,
        public string $url,
        public string $ip,
        public int $countUploadedFiles,
    ) {}
}