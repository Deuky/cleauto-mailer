<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Sensiolabs\GotenbergBundle\Processor\TempfileProcessor;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\File;
use Symfony\Component\Mailer\MailerInterface;

final class AssistanceController extends AbstractController
{
    #[Route('/depannage', methods: 'GET', name: 'app_assistance_get')]
    public function index(): Response
    {
        return $this->render('app/index.html.twig');
    }

    #[Route('/depannage', methods: 'POST', name: 'app_assistance_post')]
    public function post(MailerInterface $mailer, string $requestFrom, string $requestTo): Response
    {
        $dto = $this->dtoService->getDto();
        $biometry = $dto->agreement->rgpd->biometry;
        $signature = $biometry ?? $biometry->signature;

        if ( !$signature ) {
            throw new \UnexpectedValueException('No Signature');
        }

        $entities = $this->serviceFactory->factory($dto);

        $generate = $this->gotenbergPdfService->html()
            ->content('assistance/pdf.html.twig', (array) $entities)
            ->processor(new TempfileProcessor())
            ->generate()
            ->process();

        $filename = stream_get_meta_data($generate)['uri'];

        $pdf = $this->pdfEncryptionService->encrypt($filename);

        $email = (new Email())
            ->from($requestFrom)
            ->to($requestTo)
            ->subject('CleAuto - Dépannage');

        $email->addPart(
            new DataPart(
                new File($pdf),
                'item.pdf'
            )
        );

        $rawData = tmpfile();
        fwrite($rawData, json_encode($entities));

        $email->addPart((new DataPart($rawData, 'raw-data.json', 'application/json'))->asInline());

        $mailer->send(
            $email->html(
                $this->renderView('assistance/email.html.twig')
            )
        );

        return $this->json(['Accept']);
    }

    #[Route('/depannage/preview', methods: 'POST', name: 'app_assistance_preview')]
    public function preview(): Response
    {
        $dto = $this->dtoService->getDto();
        $biometry = $dto->agreement->rgpd->biometry;
        $signature = $biometry ?? $biometry->signature;

        if ( !$signature ) {
            throw new \UnexpectedValueException('No Signature');
        }

        $entities = $this->serviceFactory->factory($dto);

        return $this->gotenbergPdfService->html()
            ->content('assistance/pdf.html.twig', (array) $entities)
            ->processor(new TempfileProcessor())
            ->generate()
            ->stream();
    }
}