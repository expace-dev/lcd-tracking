<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'show_header' => true,
        ]);
    }

    #[Route('/mentions-legales', name: 'app_legal_mentions', methods: ['GET'])]
    public function mentionsLegales(): Response
    {
        // Option A: contenu en dur dans Twig
        return $this->render('home/mentions_legales.html.twig');

        // Option B (recommandée): passer des infos depuis la config (voir plus bas)
        // return $this->render('legal/mentions_legales.html.twig', [
        //     'legal' => [
        //         'site_name' => 'LCD Tracking',
        //         'publisher' => '...',
        //     ],
        // ]);
    }

     #[Route('/conditions-generales', name: 'app_conditions_generales', methods: ['GET'])]
    public function conditionsGenerales(): Response
    {
        // Option A: contenu en dur dans Twig
        return $this->render('home/cgu.html.twig');

        // Option B (recommandée): passer des infos depuis la config (voir plus bas)
        // return $this->render('legal/mentions_legales.html.twig', [
        //     'legal' => [
        //         'site_name' => 'LCD Tracking',
        //         'publisher' => '...',
        //     ],
        // ]);
    }

     #[Route('/politique-confidentialite', name: 'app_privacy_policy', methods: ['GET'])]
    public function privacyPolicy(): Response
    {
        // Option A: contenu en dur dans Twig
        return $this->render('home/privacy.html.twig');

        // Option B (recommandée): passer des infos depuis la config (voir plus bas)
        // return $this->render('legal/mentions_legales.html.twig', [
        //     'legal' => [
        //         'site_name' => 'LCD Tracking',
        //         'publisher' => '...',
        //     ],
        // ]);
    }

    
}
