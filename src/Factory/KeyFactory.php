<?php

namespace App\Factory;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use App\Dto\KeyDto;
use App\Entity\Key;

#[AutoconfigureTag('app.factory')]
class KeyFactory
{
    public static function createFromDto(KeyDto $dto): Key
    {
        return new Key(
            $dto->isHandFree,
            $dto->attachments,
        );
    }
}