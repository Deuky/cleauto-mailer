<?php

namespace App\Entity;

use Symfony\Component\HttpFoundation\File\File;

readonly class Assistance
{
    public function __construct(
        public string $dossierId,
        public File $idCard,
        public File $greyCard
    ) {}
}