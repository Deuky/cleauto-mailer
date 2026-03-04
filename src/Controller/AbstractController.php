<?php

namespace App\Controller;

use App\Service\ServiceFactory;
use App\Service\ServiceDto;
use App\Service\ServiceReference;
use App\Service\PdfEncryptionService;
use Sensiolabs\GotenbergBundle\GotenbergPdfInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as BaseController;

abstract class AbstractController extends BaseController
{
    public function __construct(
        public readonly ServiceFactory $serviceFactory,
        public readonly ServiceReference $referenceService,
        public readonly ServiceDto $dtoService,
        public readonly GotenbergPdfInterface $gotenbergPdfService,
        public readonly PdfEncryptionService $pdfEncryptionService,
    ){}
}