<?php

namespace App\Entity;

readonly class Key
{
    public function __construct(
        public bool $isHandFree,
        public array $attachments,
    ) {}
}