<?php

namespace App\Dto;

readonly class RequestDto
{
    public function __construct(
        public bool $repairKey,
        public bool $copyKey,
        public bool $commandWorks,
        public bool $allKeyLost,
        public bool $carOpened,
    ) {}
}
