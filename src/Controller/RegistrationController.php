<?php

namespace App\Controller;

use App\Security\AppAuthenticator;
use App\Entity\User;
use App\Form\RegistrationType;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

final class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register', methods: ['GET', 'POST'])]
    public function register(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher,
        EmailVerifier $emailVerifier,
        UserAuthenticatorInterface $userAuthenticator,
        AppAuthenticator $authenticator,
    ): Response {
        $user = new User();
        $user->setRoles(['ROLE_OWNER']);

        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = (string) $form->get('plainPassword')->getData();
            $user->setPassword($hasher->hashPassword($user, $plainPassword));

            $em->persist($user);
            $em->flush();

            // Envoi mail de vérification (non bloquant)
            $emailVerifier->sendEmailConfirmation($user);

            // Auto-login + redirect onboarding
            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('registration/register.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email', methods: ['GET'])]
    public function verifyEmail(
        Request $request,
        EmailVerifier $emailVerifier,
        EntityManagerInterface $em,
    ): Response {
        $id = $request->query->get('id');
        if (!$id) {
            throw $this->createNotFoundException();
        }

        $user = $em->getRepository(User::class)->find($id);
        if (!$user instanceof User) {
            throw $this->createNotFoundException();
        }

        $emailVerifier->handleEmailConfirmation($request, $user);
        $em->flush();

        $this->addFlash('success', 'Email confirmé ✅');

        // non bloquant : on renvoie vers onboarding/dashboard
        return $this->redirectToRoute('owner_onboarding');
    }
}
