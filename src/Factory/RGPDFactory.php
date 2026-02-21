<?php

namespace App\Factory;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use App\Dto\RGPDDto;
use App\Entity\RGPD;
use Symfony\Component\HttpFoundation\RequestStack;
use DateTimeImmutable;

#[AutoconfigureTag('app.factory')]
class RGPDFactory
{
    public function __construct(
        public readonly RequestStack $requestStack
    ){}

    public function createFromDto(RGPDDto $dto): RGPD
    {
        $request = $this->requestStack->getCurrentRequest();

        return new RGPD(
            $dto->status,
            $dto->content,
            $dto->requestDate,
            new DateTimeImmutable(),
            $request->getUri(),
            $request->server->get('REMOTE_ADDR') ?? $request->getClientIp(),
            $request->files->count(),
        );
    }
}