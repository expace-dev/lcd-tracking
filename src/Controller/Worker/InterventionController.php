<?php

namespace App\Controller\Worker;

use App\Entity\Intervention;
use App\Entity\InterventionPhoto;
use App\Form\Worker\InterventionType;
use App\Repository\InterventionRepository;
use App\Repository\PropertyRepository;
use App\Repository\WorkerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\Repository\InterventionPhotoRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;

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
        Request $request,
        WorkerRepository $workerRepository,
        InterventionRepository $interventionRepository,
        EntityManagerInterface $em,
        SluggerInterface $slugger,
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

        if ($intervention->isConfirmed()) {
            return $this->render('worker/intervention_confirmed.html.twig', [
                'worker' => $worker,
                'intervention' => $intervention,
                'property' => $intervention->getProperty(),
            ]);
        }

        $form = $this->createForm(InterventionType::class, $intervention);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // --- Upload photos (unmapped) ---
            /** @var UploadedFile[] $files */
            $files = $form->has('newPhotos') ? ($form->get('newPhotos')->getData() ?? []) : [];

            $existingCount = $intervention->getPhotos()->count();
            $incomingCount = \is_array($files) ? \count($files) : 0;

            if ($existingCount + $incomingCount > 10) {
                $this->addFlash('error', 'Maximum 10 photos par intervention.');

                return $this->redirectToRoute('worker_intervention_show', [
                    'token' => $token,
                    'id' => $intervention->getId(),
                ]);
            }

            if ($incomingCount > 0) {
                $projectDir = (string) $this->getParameter('kernel.project_dir');
                $uploadDir = $projectDir . '/public/uploads/interventions/' . $intervention->getId();

                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0775, true);
                }

                foreach ($files as $file) {
                    if (!$file instanceof UploadedFile) {
                        continue;
                    }

                    $original = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $safe = $slugger->slug($original)->lower();
                    $ext = $file->guessExtension() ?: 'jpg';
                    $filename = $safe . '-' . bin2hex(random_bytes(6)) . '.' . $ext;

                    $file->move($uploadDir, $filename);

                    $photo = new InterventionPhoto();
                    $photo->setPath('uploads/interventions/' . $intervention->getId() . '/' . $filename);

                    $intervention->addPhoto($photo);
                }
            }

            // Persist final
            $em->flush();

            $this->addFlash('success', 'Fiche enregistrée.');

            return $this->redirectToRoute('worker_intervention_show', [
                'token' => $token,
                'id' => $intervention->getId(),
            ]);
        }

        return $this->render('worker/intervention_form.html.twig', [
            'token' => $token,
            'worker' => $worker,
            'intervention' => $intervention,
            'property' => $intervention->getProperty(),
            'form' => $form,
        ]);
    }

    #[Route('/w/{token}/photo/{id}/delete', name: 'worker_photo_delete', methods: ['POST'])]
    public function deletePhoto(
        string $token,
        int $id,
        Request $request,
        WorkerRepository $workerRepository,
        InterventionPhotoRepository $photoRepository,
        EntityManagerInterface $em,
    ): RedirectResponse {
        $worker = $workerRepository->findOneByAccessToken($token);
        if (!$worker) {
            throw $this->createNotFoundException();
        }

        $photo = $photoRepository->find($id);
        if (!$photo) {
            throw $this->createNotFoundException();
        }

        $intervention = $photo->getIntervention();

        // sécurité : l’intervenant ne peut toucher que ses interventions
        if ($intervention->getCreatedBy()->getId() !== $worker->getId()) {
            throw $this->createNotFoundException();
        }

        if (!$this->isCsrfTokenValid('delete_photo_' . $photo->getId(), (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException();
        }

        // suppression fichier disque (best effort)
        $fullPath = $this->getParameter('kernel.project_dir') . '/public/' . $photo->getPath();
        if (is_file($fullPath)) {
            @unlink($fullPath);
        }

        $em->remove($photo);
        $em->flush();

        $this->addFlash('success', 'Photo supprimée.');

        return $this->redirectToRoute('worker_intervention_show', [
            'token' => $token,
            'id' => $intervention->getId(),
        ]);
    }
}
