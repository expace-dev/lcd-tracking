<?php

namespace App\Controller\Owner;

use App\Entity\User;
use App\Repository\InterventionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/owner', name: 'owner_')]
final class InterventionController extends AbstractController
{
    #[Route('/interventions/{id}', name: 'intervention_show', methods: ['GET'])]
    public function show(
        int $id,
        InterventionRepository $interventionRepository,
    ): Response {
        $owner = $this->getUser();
        if (!$owner instanceof User) {
            throw $this->createAccessDeniedException();
        }

        $intervention = $interventionRepository->findOneForOwner($id, $owner);
        if (!$intervention) {
            throw $this->createNotFoundException();
        }

        return $this->render('owner/intervention_show.html.twig', [
            'intervention' => $intervention,
            'property' => $intervention->getProperty(),
            'worker' => $intervention->getCreatedBy(),
        ]);
    }
}
