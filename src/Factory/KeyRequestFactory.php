<?php

namespace App\Factory;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use App\Dto\RequestDto;
use App\Entity\KeyRequest;

#[AutoconfigureTag('app.factory')]
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