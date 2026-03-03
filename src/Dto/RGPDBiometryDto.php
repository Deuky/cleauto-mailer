<?php

namespace App\Dto;

use DateTimeInterface;
use App\Interface\DtoInterface;
use Symfony\Component\Validator\Constraints as Assert;

readonly class RGPDBiometryDto implements DtoInterface
{
    public function __construct(
        #[Assert\IsTrue(message: "Vous devez accepter les conditions.")]
        public bool $status,
        public string $content,
        public DateTimeInterface $requestDate,

        #[Assert\Valid]
        public BiometryDto $biometry,
    ) {}
}
