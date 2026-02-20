<?php

namespace App\Factory;

use App\Dto\PersonalDto;
use App\Entity\Personal;

class PersonalFactory
{
    public static function createFromDto(PersonalDto $dto): Personal
    {
        return new Personal(
            $dto->name,
            $dto->phone,
            $dto->email,
        );
    }
}