<?php

namespace App\Factory;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use App\Dto\RgpdDto;
use App\Entity\RGPD;

#[AutoconfigureTag('app.factory')]
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