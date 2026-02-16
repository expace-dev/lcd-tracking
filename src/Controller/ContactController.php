<?php

namespace App\Controller;

use App\Entity\ContactMessage;
use App\Form\ContactType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact', methods: ['GET', 'POST'])]
    public function __invoke(
        Request $request,
        #[Autowire(service: 'limiter.contact')] RateLimiterFactory $contactLimiter,
        EntityManagerInterface $em,
        MailerInterface $mailer,
        string $contactToEmail,
        string $contactFromEmail
    ): Response {
        // Rate limit (par IP)
        $limiter = $contactLimiter->create($request->getClientIp() ?? 'anon');
        $limit = $limiter->consume(1);
        if (!$limit->isAccepted()) {
            return $this->render('contact/index.html.twig', [
                'form' => $this->createForm(ContactType::class, new ContactMessage())->createView(),
                'rate_limited' => true,
                'retry_after' => $limit->getRetryAfter()?->getTimestamp(),
            ]);
        }

        $message = new ContactMessage();
        $form = $this->createForm(ContactType::class, $message, [
            'method' => 'POST',
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Honeypot
            $honeypot = (string) $form->get('website')->getData();
            if ($honeypot !== '') {
                // On “fait comme si” c’était OK (ne pas aider les bots)
                $this->addFlash('success', 'Merci ! Votre message a bien été envoyé.');
                return $this->redirectToRoute('app_contact');
            }

            $message->setIp($request->getClientIp());
            $message->setUserAgent($request->headers->get('User-Agent'));

            $em->persist($message);
            $em->flush();

            // Email de notification
            $email = (new TemplatedEmail())
                ->from(new Address($contactFromEmail, 'LCD Tracking'))
                ->to($contactToEmail)
                ->replyTo(new Address($message->getEmail(), $message->getName()))
                ->subject('Nouveau message de contact')
                ->htmlTemplate('emails/contact.html.twig')
                ->context([
                    'contact' => $message,
                ])
            ;

            $mailer->send($email);

            $this->addFlash('success', 'Merci ! Votre message a bien été envoyé.');
            return $this->redirectToRoute('app_contact');
        }

        return $this->render('contact/index.html.twig', [
            'form' => $form->createView(),
            'rate_limited' => false,
            'retry_after' => null,
        ]);
    }
}
