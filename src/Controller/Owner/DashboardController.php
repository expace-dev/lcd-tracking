<?php

namespace App\Controller\Owner;

use App\Repository\InterventionRepository;
use App\Repository\PropertyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/owner', name: 'owner_')]
final class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'dashboard')]
    public function index(

    
        PropertyRepository $propertyRepository,
        InterventionRepository $interventionRepository,
    ): Response {
        $owner = $this->getUser();



        // Période KPI : 14 jours glissants
        $since = (new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris')))
            ->sub(new \DateInterval('P14D'));

        $properties = $propertyRepository->findByOwner($owner);

        $propertiesCount = count($properties);

        $interventionsCount = $interventionRepository
            ->countByOwnerSince($owner, $since);

        $nonConformCount = $interventionRepository
            ->countNonConformByOwnerSince($owner, $since);

        // Dernière intervention par logement (map propertyId => Intervention)
        $lastInterventions = $interventionRepository
            ->findLastByProperties($properties);

        return $this->render('owner/dashboard.html.twig', [
            'propertiesCount'   => $propertiesCount,
            'interventionsCount'=> $interventionsCount,
            'nonConformCount'   => $nonConformCount,
            'properties'        => $properties,
            'lastInterventions' => $lastInterventions,
        ]);
    }
}