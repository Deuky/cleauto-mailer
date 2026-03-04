<?php

namespace App\Factory;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use App\Dto\RGPDDto;
use App\Dto\RGPDBiometryDto;
use App\Entity\RGPD;
use Symfony\Component\HttpFoundation\RequestStack;
use DateTimeImmutable;

#[AutoconfigureTag('app.factory')]
class RGPDFactory
{
    public function __construct(
        public readonly RequestStack $requestStack,
        public readonly BiometryFactory $biometryFactory,
    ){}

    public function createFromDto(RGPDDto|RGPDBiometryDto $dto): RGPD
    {
        $request = $this->requestStack->getCurrentRequest();

        $files = $request->files->all();
        $nbrFile = 0;
        array_walk_recursive($files, function() use (&$nbrFile){
            $nbrFile ++;
        });

        return new RGPD(
            $dto->status,
            $dto->content,
            $dto->requestDate,
            new DateTimeImmutable(),
            $request->headers->get('referer') ?? "",
            $request->server->get('REMOTE_ADDR') ?? $request->getClientIp(),
            $nbrFile,

            $this->biometryFactory->createFromDto($dto->biometry ?? null)
        );
    }
}