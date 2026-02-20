<?php

namespace App\Factory;

use App\Dto\RgpdDto;
use App\Entity\RGPD;

class RgpdFactory
{
    public static function createFromDto(RgpdDto $dto, string $requestTraitDate, string $url, string $ip, int $countUploadedFiles): RGPD
    {
        return new RGPD(
            $dto->status,
            $dto->content,
            $dto->requestDate,
            $requestTraitDate,
            $url,
            $ip,
            $countUploadedFiles,
        );
    }
}