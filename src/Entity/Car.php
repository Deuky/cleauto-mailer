<?php

namespace App\Entity;

use App\Enum\FuelType;
use DateTimeInterface;

readonly class Car
{
    public function __construct(
        public string $brand,
        public string $model,
        public FuelType $fuel,
        public string $VIN,
        public DateTimeInterface $firstRegistration,
        public string $address,
        public array $attachments = [],
    ) {}
}