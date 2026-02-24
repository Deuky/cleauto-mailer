<?php

namespace App\Entity;

readonly class Agreement
{
    public function __construct(
        public RGPD $rgpd
    ) {}
}