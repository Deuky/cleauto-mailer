<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use App\Interface\DtoInterface;

readonly class PostAssistanceDto implements DtoInterface
{
    public function __construct(
        public string $dossierId,
        public array $attachments, 

    	#[Assert\Valid]
        public AgreementSensibleDto $agreement,
    ) {}
}