<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;
use App\Interface\DtoInterface;

readonly class AgreementSensibleDto implements DtoInterface
{
    public function __construct(
        #[Assert\Valid]
        public RGPDBiometryDto $rgpd,
    ) {}
}
