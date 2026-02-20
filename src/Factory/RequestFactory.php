<?php

namespace App\Factory;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use App\Dto\RequestDto;
use App\Entity\Request;

#[AutoconfigureTag('app.factory')]
class RequestFactory
{
    public function createFromDto(RequestDto $dto): Request
    {
        return new Request(
            $dto->repairKey,
            $dto->copyKey,
            $dto->commandWorks,
            $dto->allKeyLost,
            $dto->carOpened,
        );
    }
}