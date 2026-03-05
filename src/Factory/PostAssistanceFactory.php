<?php

namespace App\Factory;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use App\Dto\PostAssistanceDto;
use App\Entity\Agreement;
use App\Entity\Extra;
use App\Entity\Assistance;
use stdClass;

#[AutoconfigureTag('app.factory', ['DTO' => PostAssistanceDto::class])]
class PostAssistanceFactory
{
    public function __construct(
        public readonly AgreementFactory $agreementFactory,
    ){}

    public function createFromDto(
        PostAssistanceDto $dto
    ): stdClass
    {
        $assistance = new Assistance(
            dossierId: $dto->dossierId,
            idCard: $dto->attachments['idCard'] ?? null,
            greyCard: $dto->attachments['grayCard'] ?? null,
        );
        $agreement = $this->agreementFactory->createFromDto($dto->agreement);

        $extra = new Extra($dto->extra['informations'] ?? '');

        return (Object) [
            'assistance' => $assistance,
            'agreement' => $agreement,
        ];
    }
}
