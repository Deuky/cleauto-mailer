<?php

namespace App\Entity;

readonly class Request
{
    public function __construct(
        public bool $repairKey,
        public bool $copyKey,
        public bool $commandWorks,
        public bool $allKeyLost,
        public bool $carOpened,
    ) {}
}