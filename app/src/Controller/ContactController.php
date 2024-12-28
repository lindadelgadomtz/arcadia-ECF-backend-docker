<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController{

    
    #[Route('api/contact/submit', name: 'contact_submit', methods: ['POST'])]
    public function submit(Request $request, MailerInterface $mailer): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $title = $data['title'] ?? '';
        $description = $data['description'] ?? '';
        $email = $data['email'] ?? '';

        // Validate data
        if (empty($title) || empty($description) || empty($email)) {
            return new JsonResponse(['message' => 'All fields are required'], 400);
        }

        // Send an email
        $emailMessage = (new Email())
            ->from($email)
            ->to('fb008d3201-2efad6@inbox.mailtrap.io')
            ->subject('New Contact Request: ' . $title)
            ->text("Description: $description\n\nFrom: $email");

        try {
            $mailer->send($emailMessage);
            return new JsonResponse(['message' => 'Thank you! Your message has been sent.']);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'An error occurred while sending your message: ' . $e->getMessage()], 500);
        }
    }
}
