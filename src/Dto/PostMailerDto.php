<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

use App\Interface\DtoInterface;

readonly class PostMailerDto implements DtoInterface
{
    public function __construct(
    	#[Assert\Valid]
        public PersonalDto $personal,

        #[Assert\Valid]
        public CarDto $car,

        #[Assert\Valid]
        public KeyDto $key,

        #[Assert\Valid]
        public RequestDto $request,
		#[Assert\Collection(
            fields: [
                'informations' => new Assert\Optional([new Assert\Type('string')])
            ],
            allowExtraFields: false 
        )]
        public array $extra,

        #[Assert\Valid]
        public AgreementDto $agreement,
    ) {}
}