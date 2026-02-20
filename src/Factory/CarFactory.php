<?php

namespace App\Factory;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use App\Dto\CarDto;
use App\Entity\Car;

#[AutoconfigureTag('app.factory')]
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