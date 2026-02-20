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
    public function preview(Request $request, MailerInterface $mailer, string $requestFrom, string $requestTo, SerializerInterface $serializer, #[MapRequestPayload] PostMailerDto $dto): Response
    {
        // Create entities from DTO using factories
        $personalEntity = PersonalFactory::createFromDto($dto->personal);
        $carEntity = CarFactory::createFromDto($dto->car);
        $keyEntity = KeyFactory::createFromDto($dto->key);
        $keyRequestEntity = KeyRequestFactory::createFromDto($dto->request);

        $rgpdEntity = RgpdFactory::createFromDto(
            $dto->agreement->rgpd,
            (new DateTime())->format(DATE_W3C),
            $request->getUri(),
            $request->server->get('REMOTE_ADDR') ?? $request->getClientIp(),
            0
        );

        $extraEntity = new Extra($dto->extra['informations'] ?? '');

        // Build params array expected by the template (template uses array keys with hyphens)
        $params = [
            'personal' => [
                'name' => $personalEntity->name,
                'phone' => $personalEntity->phone,
                'email' => $personalEntity->email,
            ],
            'car' => [
                'brand' => $carEntity->brand,
                'model' => $carEntity->model,
                'fuel' => $carEntity->fuel,
                'VIN' => $carEntity->VIN,
                'first-registration' => $carEntity->firstRegistration instanceof \DateTimeInterface ? $carEntity->firstRegistration->format(DATE_W3C) : (string) $carEntity->firstRegistration,
                'address' => $carEntity->address,
                'attachments' => $carEntity->attachments,
            ],
            'key' => [
                'is-hand-free' => $keyEntity->isHandFree,
                'attachments' => $keyEntity->attachments,
            ],
            'request' => [
                'repair-key' => $keyRequestEntity->repairKey,
                'copy-key' => $keyRequestEntity->copyKey,
                'command-works' => $keyRequestEntity->commandWorks,
                'all-key-lost' => $keyRequestEntity->allKeyLost,
                'car-opened' => $keyRequestEntity->carOpened,
            ],
            'extra' => [
                'informations' => $extraEntity->informations,
            ],
            'agreement' => [
                'rgpd' => [
                    'status' => $rgpdEntity->status,
                    'content' => $rgpdEntity->content,
                    'request-date' => $rgpdEntity->requestDate instanceof \DateTimeInterface ? $rgpdEntity->requestDate->format(DATE_W3C) : (string) $rgpdEntity->requestDate,
                    'request-trait-date' => $rgpdEntity->requestTraitDate,
                    'url' => $rgpdEntity->url,
                    'ip' => $rgpdEntity->ip,
                    'count-uploaded-files' => $rgpdEntity->countUploadedFiles,
                ],
            ],
        ];

        return $this->render('mailer/car-request.html.twig', $params);
    }
}
