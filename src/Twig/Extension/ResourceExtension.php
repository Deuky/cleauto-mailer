<?php

namespace App\Twig\Extension;

use Twig\Attribute\AsTwigFilter;
use Symfony\Component\HttpFoundation\File\File;

class ResourceExtension
{
    #[AsTwigFilter('imgSrcBase64')]
    public function formatImgSrcBase64(File $file): string
    {
    	$mime = mime_content_type($file->getPathName());

    	return 'data:'.$mime.';base64,'.base64_encode(file_get_contents($file->getPathName()));
    }
}