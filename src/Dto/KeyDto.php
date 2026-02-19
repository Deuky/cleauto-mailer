<?php

namespace App\Dto;

readonly class KeyDto
{
    public function __construct(
        public bool $isHandFree,
        public array $attachments = [],
    ) {}
}
