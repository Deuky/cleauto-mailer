<?php

namespace App\Service;

use App\Docker\Container;

class DockerService
{
    public function create(array $data)
    {
        $container = new Container($data);
        $container->create();

        return $container;
    }
}