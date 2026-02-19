<?php

namespace App\Dto;

readonly class PostMailerDto
{
    public function __construct(
        public array $personal,
        public array $car,
        public array $key,
        public array $request,
        public array $extra,
        public array $agreement,
    ) {}
}