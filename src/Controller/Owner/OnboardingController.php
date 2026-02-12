<?php

namespace App\Controller\Owner;

use App\Entity\User;
use App\Repository\PropertyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/owner', name: 'owner_')]
final class OnboardingController extends AbstractController
{
    #[Route('/onboarding', name: 'onboarding', methods: ['GET'])]
    public function index(PropertyRepository $propertyRepository): Response
    {
        $owner = $this->getOwner();

        $properties = $propertyRepository->findByOwner($owner);

        $hasProperty = \count($properties) > 0;

        // "Intervenant lié" (ManyToMany User<->Worker)
        $hasWorker = $owner->getWorkers()->count() > 0;

        // "Assigné à un logement" (pour l’étape 3)
        $hasAssignment = false;
        foreach ($properties as $property) {
            if (null !== $property->getAssignedWorker()) {
                $hasAssignment = true;
                break;
            }
        }

        return $this->render('owner/onboarding.html.twig', [
            'hasProperty'   => $hasProperty,
            'hasWorker'     => $hasWorker,
            'hasAssignment' => $hasAssignment,
        ]);
    }

    private function getOwner(): User
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }

        return $user;
    }
}
