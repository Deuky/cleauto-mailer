<?php

namespace App\Dto;

use App\Interface\DtoInterface;

readonly class KeyDto implements DtoInterface
{
    public function __construct(
        public bool $isHandFree,
        public array $attachments = [],
    ) {}
}
