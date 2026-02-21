<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;
use App\Dto\PostMailerDto;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use App\Service\ServiceFactory;

final class MailerController extends AbstractController
{
    public function __construct(
        public readonly ServiceFactory $serviceFactory
    ){}

    #[Route('/', methods: 'GET', name: 'app_mailer_get')]
    public function index(MailerInterface $mailer): Response
    {
        return $this->render('app/index.html.twig');
    }

    #[Route('/mailer', methods: 'POST', name: 'app_mailer_post')]
    public function post(
        #[MapRequestPayload] 
        PostMailerDto $dto, 
        MailerInterface $mailer, 
        string $requestFrom, 
        string $requestTo
    ): JsonResponse
    {
        $entites = $this->serviceFactory->factory($dto);

        $email = (new Email())
            ->from($requestFrom)
            ->to($requestTo)
            ->subject('CleAuto - Demande d\'intervention');

        array_walk_recursive([$entities->key->attachments, $entities->car->attachments], function($file) {
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
        fwrite($rawData, json_encode($params));

        $email->addPart((new DataPart($rawData, 'raw-data.json', 'application/json'))->asInline());

        $mailer->send($email->html(
                $this->renderView('mailer/car-request.html.twig', (array) $entities)
            ));

        return $this->json(["Accept"]);
    }

    #[Route('/mailer/preview', methods: 'POST', name: 'app_mailer_preview')]
    public function preview(
        #[MapRequestPayload] 
        PostMailerDto $dto,
    ): Response
    {
        return $this->render('mailer/car-request.html.twig', (array) $this->serviceFactory->factory($dto));
    }
}
