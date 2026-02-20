<?php

namespace App\Controller;

use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Dto\PostMailerDto;
use App\Entity\Car;
use App\Entity\Personal;
use App\Entity\Key;
use App\Entity\KeyRequest;
use App\Entity\RGPD;
use App\Entity\Extra;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Serializer\SerializerInterface;
use App\Factory\PersonalFactory;
use App\Factory\CarFactory;
use App\Factory\KeyFactory;
use App\Factory\KeyRequestFactory;
use App\Factory\RgpdFactory;
use App\Service\ServiceFactory;

final class MailerController extends AbstractController
{
    #[Route('/', methods: 'GET', name: 'app_mailer_get')]
    public function index(MailerInterface $mailer): Response
    {
        return $this->render('app/index.html.twig');
    }

    #[Route('/mailer', methods: 'POST', name: 'app_mailer_post')]
    public function post(Request $request, MailerInterface $mailer, string $requestFrom, string $requestTo): JsonResponse
    {
        $params = $request->getPayload()->all();
        $params['agreement']['rgpd']['ip'] = $request->server->get('REMOTE_ADDR') ?? $request->getClientIp();
        $params['agreement']['rgpd']['count-uploaded-files'] = 0;
        $params['agreement']['rgpd']['request-trait-date'] = (new DateTime())->format(DATE_W3C);
        $params['key']['attachments'] = $request->files->get('key')['attachments'] ?? [];
        $params['car']['attachments'] = $request->files->get('car')['attachments'] ?? [];
        $attachments = [
            $params['key']['attachments'],
            $params['car']['attachments']
        ];

        $email = (new Email())
            ->from($requestFrom)
            ->to($requestTo)
            ->subject('CleAuto - Demande d\'intervention');

        array_walk_recursive($attachments, function($file) use (&$params, $email) {
            $email->addPart(
                new DataPart(
                    new File($file),
                    implode('.', [
                            'item',
                            $params['agreement']['rgpd']['count-uploaded-files']++,
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
                $this->renderView('mailer/car-request.html.twig', $params)
            ));

        return $this->json(["Accept"]);
    }

    #[Route('/mailer/preview', methods: 'POST', name: 'app_mailer_preview')]
    public function preview(Request $request, MailerInterface $mailer, string $requestFrom, string $requestTo, #[MapRequestPayload] PostMailerDto $dto,
        ServiceFactory $sf
        ): Response
    {
        return $this->render('mailer/car-request.html.twig', (array) $sf->factory($dto));
    }
}
