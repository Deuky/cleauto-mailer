<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;
use App\Service\ServiceFactory;
use App\Service\ServiceDto;
use App\Service\ServiceReference;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\File;

final class MailerController extends AbstractController
{
    public function __construct(
        public readonly ServiceFactory $serviceFactory,
        public readonly ServiceReference $referenceService,
        public readonly ServiceDto $dtoService,
    ){}

    #[Route('/', methods: 'GET', name: 'app_mailer_get')]
    public function index(MailerInterface $mailer): Response
    {
        return $this->render('app/index.html.twig');
    }

    #[Route('/mailer', methods: 'POST', name: 'app_mailer_post')]
    public function post(
        MailerInterface $mailer, 
        string $requestFrom, 
        string $requestTo
    ): JsonResponse
    {
        $dto = $this->dtoService->getDto();
        $entities = $this->serviceFactory->factory($dto);
        $identification = $this->referenceService->getIdentification($entities->personal, $entities->car, $entities->agreement->rgpd);
        $caseNumber = implode(' ', $identification);

        $email = (new Email())
            ->from($requestFrom)
            ->to($requestTo)
            ->subject('CleAuto - Demande d\'intervention - '. $caseNumber);

        $attachments = [$entities->key->attachments, $entities->car->attachments];

        array_walk_recursive($attachments, function($file) use ($email) {
            static $i = 0;

            $email->addPart(
                new DataPart(
                    new File($file),
                    implode('.', [
                            'item',
                            $i++,
                            $file->guessClientExtension()
                        ]
                    )
                )
            );
        });

        $rawData = tmpfile();
        fwrite($rawData, json_encode($dto));

        $email->addPart((new DataPart($rawData, 'raw-data.json', 'application/json'))->asInline());

        $mailer->send($email->html(
                $this->renderView('mailer/car-request.html.twig', (array) $entities + ['caseNumber' => $caseNumber])
            ));

        return $this->json(["Accept"]);
    }

    #[Route('/mailer/preview', methods: 'POST', name: 'app_mailer_preview')]
    public function preview(): Response
    {
        $entities = $this->serviceFactory->factory(
                $this->dtoService->getDto()
            );

        $identification = $this->referenceService->getIdentification($entities->personal, $entities->car, $entities->agreement->rgpd);

        return $this->render(
            'mailer/car-request.html.twig', 
            (array) $entities + ['caseNumber' => implode(' ', $identification)]
        );
    }
}
