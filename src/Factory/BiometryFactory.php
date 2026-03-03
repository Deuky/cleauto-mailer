<?php

namespace App\Factory;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use App\Dto\BiometryDto;
use App\Entity\Biometry;
use App\Entity\Resource;

#[AutoconfigureTag('app.factory', ['DTO' => BiometryDto::class])]
class BiometryFactory
{
    public function createFromDto(?BiometryDto $dto): Biometry
    {
        return new Biometry(
            signature: $dto->signature ? $this->getResource($dto->signature) : null
        );
    }

    public function getResource(string $signature): Resource
    {
        $t = tmpfile();
        fwrite($t, $signature);
        rewind($t);
        flock($t, LOCK_EX);

        return new Resource(
            resource: $t
        );
    }
}