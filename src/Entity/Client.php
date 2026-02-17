<?php

namespace App\Entity;

class Client
{
    public function __construct(
        public readonly string $nom,
        public readonly string $prenom,
        public readonly string $telephone,
        public readonly string $email,
    ) {
    }
}
