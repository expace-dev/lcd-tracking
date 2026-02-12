<?php

namespace App\Controller\Owner;

use App\Entity\User;
use App\Entity\Worker;
use App\Form\Owner\WorkerSearchType;
use App\Form\Owner\WorkerType;
use App\Repository\WorkerRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/owner/workers', name: 'owner_workers_')]
final class WorkerController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET', 'POST'])]
    public function index(
        Request $request,
        WorkerRepository $workerRepository,
    ): Response {
        $owner = $this->getOwner();

        // Liste des intervenants déjà liés à ce propriétaire
        $workers = $owner->getWorkers();

        // Form recherche téléphone
        $searchForm = $this->createForm(WorkerSearchType::class);
        $searchForm->handleRequest($request);

        $foundWorker = null;

        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $phone = (string) $searchForm->get('phone')->getData();
            $phone = $this->normalizePhone($phone);

            $foundWorker = $workerRepository->findOneBy(['phone' => $phone]);

            if ($foundWorker instanceof Worker) {
                // déjà lié => on le signale
                if ($workers->contains($foundWorker)) {
                    $this->addFlash('success', 'Intervenant déjà lié à votre compte.');
                    return $this->redirectToRoute('owner_workers_index');
                }

                // Sinon: on laisse $foundWorker à la vue pour proposer le bouton "Lier"
            } else {
                // Pas trouvé -> création avec phone prérempli
                return $this->redirectToRoute('owner_workers_new', ['phone' => $phone]);
            }
        }

        return $this->render('owner/workers/index.html.twig', [
            'workers' => $workers,
            'searchForm' => $searchForm,
            'foundWorker' => $foundWorker,
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        WorkerRepository $workerRepository,
        EntityManagerInterface $em,
    ): Response {
        $owner = $this->getOwner();

        $worker = new Worker();

        // pré-remplissage si on vient de la recherche
        $phone = (string) $request->query->get('phone', '');
        if ($phone !== '') {
            $worker->setPhone($this->normalizePhone($phone));
        }

        $form = $this->createForm(WorkerType::class, $worker);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // token obligatoire (nouvelle création)
            $worker->regenerateAccessToken();

            try {
                $em->persist($worker);
                $owner->addWorker($worker);
                $em->flush();

                $this->addFlash('success', 'Intervenant créé et lié à votre compte.');
                return $this->redirectToRoute('owner_workers_index');
            } catch (UniqueConstraintViolationException) {
                // Cas rare : un autre proprio a créé le worker (phone unique) juste avant le flush
                $em->clear(); // on repart proprement (évite un état UnitOfWork incohérent)

                $existing = $workerRepository->findOneBy(['phone' => $worker->getPhone()]);
                if ($existing instanceof Worker) {
                    if (!$owner->getWorkers()->contains($existing)) {
                        $owner->addWorker($existing);
                        $em->flush();
                    }

                    $this->addFlash('success', 'Intervenant déjà existant. Il a été lié à votre compte.');
                    return $this->redirectToRoute('owner_workers_index');
                }

                // fallback (très improbable)
                $this->addFlash('error', 'Une erreur est survenue. Merci de réessayer.');
                return $this->redirectToRoute('owner_workers_new', ['phone' => $worker->getPhone()]);
            }
        }

        return $this->render('owner/workers/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(
        int $id,
        Request $request,
        WorkerRepository $workerRepository,
        EntityManagerInterface $em,
    ): Response {
        $owner = $this->getOwner();

        $worker = $workerRepository->find($id);
        if (!$worker instanceof Worker) {
            throw $this->createNotFoundException();
        }

        // sécurité : doit être lié au owner
        if (!$owner->getWorkers()->contains($worker)) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(WorkerType::class, $worker, [
            'phone_readonly' => true, // évite de casser l’unicité / collisions
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Intervenant mis à jour.');
            return $this->redirectToRoute('owner_workers_index');
        }

        return $this->render('owner/workers/edit.html.twig', [
            'worker' => $worker,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/link', name: 'link', methods: ['POST'])]
    public function linkExisting(
        int $id,
        Request $request,
        WorkerRepository $workerRepository,
        EntityManagerInterface $em,
    ): RedirectResponse {
        $owner = $this->getOwner();

        $worker = $workerRepository->find($id);
        if (!$worker instanceof Worker) {
            throw $this->createNotFoundException();
        }

        if (!$this->isCsrfTokenValid('link_worker_' . $worker->getId(), (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException();
        }

        if (!$owner->getWorkers()->contains($worker)) {
            $owner->addWorker($worker);
            $em->flush();
        }

        $this->addFlash('success', 'Intervenant lié à votre compte.');
        return $this->redirectToRoute('owner_workers_index');
    }

    private function getOwner(): User
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }

        return $user;
    }

    private function normalizePhone(string $phone): string
    {
        return preg_replace('/\D+/', '', $phone) ?? '';
    }
}
