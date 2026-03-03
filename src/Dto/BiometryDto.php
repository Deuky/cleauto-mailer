<?php

namespace App\Dto;

use App\Interface\DtoInterface;

readonly class BiometryDto implements DtoInterface
{
    public function __construct(
    	#[Assert\NotBlank]
    	#[Assert\Regex(
            pattern: '/^\s*<svg[^>]*>.*<\/svg>\s*$/is',
            message: "Le contenu fourni n'est pas un format SVG valide."
        )]
    	public string $signature
    ) {}
}