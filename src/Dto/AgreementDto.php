<?php

namespace App\Dto;

readonly class AgreementDto
{
    public function __construct(
        public RgpdDto $rgpd,
    ) {}
}
