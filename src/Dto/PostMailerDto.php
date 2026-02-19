<?php

namespace App\Dto;

readonly class PostMailerDto
{
    public function __construct(
        public PersonalDto $personal,
        public CarDto $car,
        public KeyDto $key,
        public RequestDto $request,
        public array $extra,
        public AgreementDto $agreement,
    ) {}
}