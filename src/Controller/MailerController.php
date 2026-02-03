<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\File;

final class MailerController extends AbstractController
{
    #[Route('/mailer', methods: 'GET', name: 'app_mailer_get')]
    public function index(MailerInterface $mailer): Response
    {
        return $this->render('app/index.html.twig');
    }

    #[Route('/mailer', methods: 'POST', name: 'app_mailer_post')]
    public function post(Request $request, MailerInterface $mailer, string $requestFrom, string $requestTo): JsonResponse
    {
        $params = $request->getPayload()->all();
        $params['extra']['count_uploaded_files'] = count($request->files->get('extra')['attachments'] ?? []);

        $email = (new Email())
            ->from($requestFrom)
            ->to($requestTo)
            ->subject('CleAuto - Demande d\'intervention')
            ->html(
                $this->renderView('mailer/car-request.html.twig', $params)
            );

        foreach ($request->files->get('extra')["attachments"] as $i => $file) {
            $email->addPart(new DataPart(new File($file), "photo".$i.".jpg"));
        }

        $mailer->send($email);

        return $this->json([]);
    }
}
