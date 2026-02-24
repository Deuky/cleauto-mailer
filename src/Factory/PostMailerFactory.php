<?php

namespace App\Factory;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use App\Dto\PostMailerDto;
use App\Entity\Extra;
use stdClass;

#[AutoconfigureTag('app.factory', ['DTO' => PostMailerDto::class])]
class PostMailerFactory
{
    public function __construct(
        public readonly PersonalFactory $personalFactory,
        public readonly CarFactory $carFactory,
        public readonly KeyFactory $keyFactory,
        public readonly RequestFactory $requestFactory,
        public readonly AgreementFactory $agreementFactory,
    ){}
    /**
     * Create entities from a PostMailer DTO by dispatching to the other factories.
     *
     * Returns an associative array with keys: personal, car, key, request, agreement, extra
     *
     * @return array<string, mixed>
     */
    public function createFromDto(
        PostMailerDto $dto
    ): stdClass
    {
        $personal = $this->personalFactory->createFromDto($dto->personal);
        $car = $this->carFactory->createFromDto($dto->car);
        $key = $this->keyFactory->createFromDto($dto->key);
        $request = $this->requestFactory->createFromDto($dto->request);
        $agreement = $this->agreementFactory->createFromDto($dto->agreement);

        $extra = new Extra($dto->extra['informations'] ?? '');

        return (Object) [
            'personal' => $personal,
            'car' => $car,
            'key' => $key,
            'request' => $request,
            'agreement' => $agreement,
            'extra' => $extra,
        ];
    }
}
