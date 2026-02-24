<?php

namespace App\Dto;

use App\Interface\DtoInterface;

readonly class RequestDto implements DtoInterface
{
    public function __construct(
        public bool $repairKey,
        public bool $copyKey,
        public bool $commandWorks,
        public bool $allKeyLost,
        public bool $carOpened,
    ) {}
}
