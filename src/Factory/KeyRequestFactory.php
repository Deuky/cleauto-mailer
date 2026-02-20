<?php

namespace App\Factory;

use App\Dto\RequestDto;
use App\Entity\KeyRequest;

class KeyRequestFactory
{
    public static function createFromDto(RequestDto $dto): KeyRequest
    {
        return new KeyRequest(
            $dto->repairKey,
            $dto->copyKey,
            $dto->commandWorks,
            $dto->allKeyLost,
            $dto->carOpened,
        );
    }
}