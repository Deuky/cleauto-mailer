<?php

namespace App\Factory;

use App\Dto\CarDto;
use App\Entity\Car;

class CarFactory
{
    public static function createFromDto(CarDto $dto): Car
    {
        return new Car(
            $dto->brand,
            $dto->model,
            $dto->fuel,
            $dto->VIN,
            $dto->firstRegistration,
            $dto->address,
            $dto->attachments,
        );
    }
}