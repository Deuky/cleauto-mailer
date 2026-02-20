<?php

namespace App\Service;

use App\Factory\FactoryInterface;

final class ServiceFactory
{
    /**
     * @param iterable<int, FactoryInterface> $factories
     */
    public function __construct(
        #[AutowireIterator('app.factory')]
        public readonly iterable $factories)
    {
    }

    /**
     * Retourne l'itérable des factories taguées.
     *
     * @return iterable<int, FactoryInterface>
     */
    public function getFactories(): iterable
    {
        return $this->factories;
    }
}
