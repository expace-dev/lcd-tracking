<?php

namespace App\Controller\Owner;

use App\Entity\User;
use App\Repository\InterventionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\InterventionSearchType;
use App\Model\InterventionSearch;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\Request;

#[Route('/owner', name: 'owner_')]
final class InterventionController extends AbstractController
{
    #[Route('/interventions', name: 'interventions_index', methods: ['GET'])]
    public function index(Request $request, InterventionRepository $repo): Response
    {
        $owner = $this->getUser();
        if (!$owner) {
            throw $this->createAccessDeniedException();
        }

        $search = new InterventionSearch();
        $form = $this->createForm(InterventionSearchType::class, $search, [
            'owner' => $owner,
        ]);
        $form->handleRequest($request);

        $page = max(1, (int) $request->query->get('page', 1));
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $qb = $repo->qbForOwner($owner, $search)
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        $paginator = new Paginator($qb, true);
        $total = count($paginator);
        $items = iterator_to_array($paginator);
        $hasNext = ($offset + $limit) < $total;

        $params = $request->query->all();
        $params['page'] = $page + 1;

        // Turbo Stream "charger plus"
       $isTurboStream = $request->headers->get('Turbo-Stream') === 'true';

if ($isTurboStream) {
    $response = $this->render('owner/interventions/index.stream.html.twig', [
        'interventions' => $items,
        'hasNext' => $hasNext,
        'nextUrl' => $hasNext ? $this->generateUrl('owner_interventions_index', $params) : null,
    ]);

    $response->headers->set('Content-Type', 'text/vnd.turbo-stream.html');

    return $response;
}

        return $this->render('owner/interventions/index.html.twig', [
    'form' => $form,
    'interventions' => $items,
    'hasNext' => $hasNext,
    'nextUrl' => $hasNext ? $this->generateUrl('owner_interventions_index', $params) : null,
]);
    }
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
