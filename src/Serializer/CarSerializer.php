<?php

namespace App\Serializer;

use App\Entity\Car;
use App\Entity\FuelType;
use DateTimeInterface;
use DateTimeImmutable;

class CarSerializer
{
    public function serialize(Car $car): array
    {
        return [
            'vin' => $car->vin,
            'brand' => $car->brand,
            'model' => $car->model,
            'firstRegistry' => $car->firstRegistry->format('Y-m-d'),
            'fuel' => $car->fuel->value,
        ];
    }

    public function deserialize(array $data): Car
    {
        return new Car(
            vin: $data['vin'] ?? throw new \InvalidArgumentException('Missing vin'),
            brand: $data['brand'] ?? throw new \InvalidArgumentException('Missing brand'),
            model: $data['model'] ?? throw new \InvalidArgumentException('Missing model'),
            firstRegistry: new DateTimeImmutable($data['firstRegistry'] ?? throw new \InvalidArgumentException('Missing firstRegistry')),
            fuel: FuelType::from($data['fuel'] ?? throw new \InvalidArgumentException('Missing fuel')),
        );
    }
}
