<?php

namespace App\Entity;

class VIN
{
    public function __construct(
        public readonly string $wmi,
        public readonly string $vds,
        public readonly string $checkDigit,
        public readonly string $modelYear,
        public readonly string $plantCode,
        public readonly string $serialNumber,
    ) {
    }
}
