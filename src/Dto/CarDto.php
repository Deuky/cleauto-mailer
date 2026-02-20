<?php

namespace App\Dto;

use App\Enum\FuelType;
use DateTimeInterface;
use App\Interface\DtoInterface;

readonly class CarDto implements DtoInterface
{
    public function __construct(
        public string $brand,
        public string $model,
        public FuelType $fuel,
        public string $VIN,
        public DateTimeInterface $firstRegistration,
        public string $address,
        public array $attachments = [],
    ) {
    }
}