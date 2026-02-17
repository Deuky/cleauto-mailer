<?php

namespace App\Serializer;

use App\Entity\Client;

class ClientSerializer
{
    public function serialize(Client $client): array
    {
        return [
            'nom' => $client->nom,
            'prenom' => $client->prenom,
            'telephone' => $client->telephone,
            'email' => $client->email,
        ];
    }

    public function deserialize(array $data): Client
    {
        return new Client(
            nom: $data['nom'] ?? throw new \InvalidArgumentException('Missing nom'),
            prenom: $data['prenom'] ?? throw new \InvalidArgumentException('Missing prenom'),
            telephone: $data['telephone'] ?? throw new \InvalidArgumentException('Missing telephone'),
            email: $data['email'] ?? throw new \InvalidArgumentException('Missing email'),
        );
    }
}
