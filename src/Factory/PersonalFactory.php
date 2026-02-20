<?php

namespace App\Factory;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use App\Dto\PersonalDto;
use App\Entity\Personal;

#[AutoconfigureTag('app.factory')]
class PersonalFactory
{
    public function createFromDto(PersonalDto $dto): Personal
    {
        return new Personal(
            $dto->name,
            $dto->phone,
            $dto->email,
        );
    }
}