<?php

namespace App\Factory;

use App\Dto\KeyDto;
use App\Entity\Key;

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