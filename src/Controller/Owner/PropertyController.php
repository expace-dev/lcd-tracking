<?php

namespace App\Controller\Owner;

use App\Entity\Property;
use App\Form\PropertyType;
use App\Repository\PropertyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/owner/properties', name: 'owner_properties_')]
final class PropertyController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(PropertyRepository $propertyRepository): Response
    {
        $owner = $this->getUser();
        $properties = $propertyRepository->findByOwner($owner);

        return $this->render('owner/properties/index.html.twig', [
            'properties' => $properties,
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $owner = $this->getUser();

        $property = new Property();
        $property->setOwner($owner);

        $form = $this->createForm(PropertyType::class, $property, [
            'owner' => $owner,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($property);
            $em->flush();

            $this->addFlash('success', 'Logement créé.');
            return $this->redirectToRoute('owner_properties_index');
        }

        return $this->render('owner/properties/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(
        int $id,
        Request $request,
        PropertyRepository $propertyRepository,
        EntityManagerInterface $em
    ): Response {
        $owner = $this->getUser();
        $property = $propertyRepository->findOneByIdAndOwner($id, $owner);

        if (!$property) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(PropertyType::class, $property, [
            'owner' => $owner,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Logement mis à jour.');
            return $this->redirectToRoute('owner_properties_index');
        }

        return $this->render('owner/properties/edit.html.twig', [
            'property' => $property,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['POST'])]
    public function delete(
        int $id,
        Request $request,
        PropertyRepository $propertyRepository,
        EntityManagerInterface $em
    ): Response {
        $owner = $this->getUser();
        $property = $propertyRepository->findOneByIdAndOwner($id, $owner);

        if (!$property) {
            throw $this->createNotFoundException();
        }

        if (!$this->isCsrfTokenValid('delete_property_' . $property->getId(), (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException();
        }

        // Avec ON DELETE CASCADE côté Intervention, tout part avec
        $em->remove($property);
        $em->flush();

        $this->addFlash('success', 'Logement supprimé.');
        return $this->redirectToRoute('owner_properties_index');
    }
}
