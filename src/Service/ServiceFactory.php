<?php

namespace App\Service;

use App\Factory\FactoryInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use App\Interface\DtoInterface;

final class ServiceFactory
{
    private ?array $cachedFactories = null;

    /**
     * @param iterable<int, FactoryInterface> $factories
     */
    public function __construct(
        #[AutowireIterator('app.factory', indexAttribute: 'DTO')]
        public readonly iterable $factories)
    {}

    /**
     * Retourne l'itérable des factories taguées.
     *
     * @return iterable<int, FactoryInterface>
     */
    public function getFactories(): iterable
    {
        return $this->factories;
    }

    public function getFactory(string $dto): mixed
    {
        return ($this->cachedFactories ??= iterator_to_array($this->factories))[$dto] ?? throw new \InvalidArgumentException("No factory found for DTO {$dto}");
    }

    public function factory(DtoInterface $dto, ...$args): mixed
    {
        return $this->getFactory($dto::class)->createFromDto($dto, ...$args);
    }
}
