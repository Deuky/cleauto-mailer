<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AssistanceController extends AbstractController
{
    #[Route('/depannage', methods: 'GET', name: 'app_assistance_get')]
    public function index(): Response
    {
        return $this->render('app/index.html.twig');
    }

    #[Route('/depannage', methods: 'POST', name: 'app_assistance_post')]
    public function post(): Response
    {
    	$dto = $this->dtoService->getDto();
    	$biometry = $dto->agreement->rgpd->biometry;
    	$signature = $biometry ?? $biometry->signature;

    	if ( !$signature ) {
    		throw new \UnexpectedValueException('No Signature');
    	}

        $entities = $this->serviceFactory->factory($dto);

        var_dump($this->gotenbergPdfService->html()
            ->content('assistance/index.html.twig', (array) $entities)
            ->generate()
            ->stream()
            ->getContent());

        die();

        return $this->gotenbergPdfService->html()
            ->content('assistance/index.html.twig', (array) $entities)
            ->generate()
            ->stream();
    }
}