<?php

namespace App\Entity;

use App\Enum\FuelType;
use DateTimeInterface;

class Car
{
    public function __construct(
        public readonly string $vin,
        public readonly string $brand,
        public readonly string $model,
        public readonly DateTimeInterface $firstRegistry,
        public readonly FuelType $fuel,
    ) {
    }
}
