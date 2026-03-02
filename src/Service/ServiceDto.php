<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Interface\DtoInterface;

class ServiceDto
{
    private Request $request;
    public readonly array $mapping;

    public function __construct(
        private RequestStack $requestStack,
        private SerializerInterface $serializer,
        private ValidatorInterface $validator,
        ParameterBagInterface $params
    ) {
        $this->request = $this->requestStack->getCurrentRequest();
        $this->mapping = $params->get('dto_mapping');
    }

    public function getDtoClass(?string $route): string
    {
        $route ??= $this->request->attributes->get('_route');

        if (!isset($this->mapping[$route])) {
            throw new \InvalidArgumentException("Le mapping DTO '$route' n'existe pas.");
        }

        return $this->mapping[$route]['class'] ?? throw new \InvalidArgumentException("Le mapping DTO '$route' n'a pas de class défini.");
    }

    private function getData(): array
    {
        $params = $this->request->request->all();
        array_walk_recursive(
            $params,
            fn(&$value) => $value = match($value) {
                                "false" => false,
                                "true" => true,
                                default => $value ?: null
                            }
        );

        return array_replace_recursive(
            $params,
            $this->request->files->all()
        );
    }

    /**
     * @template DtoInterface
     * @param string $mappingKey La clé définie dans dtos.yaml
     * @return DtoInterface
     */
    public function getDto(string $route = null): DtoInterface
    {
        $dtoClass = $this->getDtoClass($route);
        $data = $this->getData();

        $dto = $this->serializer->denormalize($data, $dtoClass, null, [
            AbstractObjectNormalizer::DISABLE_TYPE_ENFORCEMENT => true,
        ]);

        // Validation
        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            // On pourrait jeter une exception personnalisée ici
            throw new \RuntimeException("Validation failed: " . (string) $errors);
        }

        return $dto;
    }
}