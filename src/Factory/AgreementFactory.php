<?php

namespace App\Factory;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use App\Dto\AgreementDto;
use App\Entity\Agreement;

#[AutoconfigureTag('app.factory', ['DTO' => AgreementDto::class])]
class AgreementFactory
{
    public function __construct(
        public readonly RGPDFactory $rgpdFactory,
    ){}

    public function createFromDto(AgreementDto $dto): Agreement
    {
        return new Agreement(
            rgpd: $this->rgpdFactory->createFromDto($dto->rgpd)
        );
    }
}