<?php

namespace App\Controller\Worker;

use App\Entity\Intervention;
use App\Repository\InterventionRepository;
use App\Repository\PropertyRepository;
use App\Repository\WorkerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class InterventionController extends AbstractController
{
    #[Route('/w/{token}/property/{id}/intervention/new', name: 'worker_intervention_new', methods: ['GET'])]
    public function new(
        string $token,
        int $id,
        WorkerRepository $workerRepository,
        PropertyRepository $propertyRepository,
        InterventionRepository $interventionRepository,
        EntityManagerInterface $em,
    ): Response {
        $worker = $workerRepository->findOneByAccessToken($token);
        if (!$worker) {
            throw $this->createNotFoundException();
        }

        // Sécurité : le logement doit être assigné à cet intervenant
        $property = $propertyRepository->findOneByIdAndAssignedWorker($id, $worker);
        if (!$property) {
            throw $this->createNotFoundException();
        }

        $tz = new \DateTimeZone('Europe/Paris');
        $today = (new \DateTimeImmutable('now', $tz))->setTime(0, 0, 0);

        // Règle : max 1 intervention par logement et par jour
        $existing = $interventionRepository->findOneByPropertyAndBusinessDate($property, $today);
        if ($existing) {
            return $this->render('worker/intervention_already_done.html.twig', [
                'worker' => $worker,
                'property' => $property,
                'intervention' => $existing,
            ]);
        }

        // Création brouillon
        $intervention = new Intervention();
        $intervention->setProperty($property);
        $intervention->setCreatedBy($worker);
        $intervention->setBusinessDate($today);

        $em->persist($intervention);
        $em->flush();

        return $this->redirectToRoute('worker_intervention_show', [
            'token' => $token,
            'id' => $intervention->getId(),
        ]);
    }

    #[Route('/w/{token}/intervention/{id}', name: 'worker_intervention_show', methods: ['GET', 'POST'])]
public function show(
    string $token,
    int $id,
    \Symfony\Component\HttpFoundation\Request $request,
    WorkerRepository $workerRepository,
    InterventionRepository $interventionRepository,
    EntityManagerInterface $em,
): Response {
    $worker = $workerRepository->findOneByAccessToken($token);
    if (!$worker) {
        throw $this->createNotFoundException();
    }

    $intervention = $interventionRepository->find($id);
    if (!$intervention) {
        throw $this->createNotFoundException();
    }

    // Sécurité : l’intervenant ne doit voir/modifier que ses interventions
    if ($intervention->getCreatedBy()->getId() !== $worker->getId()) {
        throw $this->createNotFoundException();
    }

    $form = $this->createForm(\App\Form\Worker\InterventionType::class, $intervention);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $em->flush();

        $this->addFlash('success', 'Fiche enregistrée.');

        // On reste sur la même page (pratique sur mobile)
        return $this->redirectToRoute('worker_intervention_show', [
            'token' => $worker->getAccessToken(),
            'id' => $intervention->getId(),
        ]);
    }

    return $this->render('worker/intervention_form.html.twig', [
        'worker' => $worker,
        'intervention' => $intervention,
        'property' => $intervention->getProperty(),
        'form' => $form,
    ]);
}

}
