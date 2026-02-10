<?php

namespace App\Controller\Worker;

use App\Repository\PropertyRepository;
use App\Repository\WorkerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AccessController extends AbstractController
{
    #[Route('/w/{token}', name: 'worker_access', methods: ['GET'])]
    public function index(
        string $token,
        WorkerRepository $workerRepository,
        PropertyRepository $propertyRepository,
    ): Response {
        $worker = $workerRepository->findOneByAccessToken($token);

        if (!$worker) {
            throw $this->createNotFoundException();
        }

        $properties = $propertyRepository->findAssignedToWorker($worker);

        return $this->render('worker/access.html.twig', [
            'worker' => $worker,
            'properties' => $properties,
        ]);
    }
}
