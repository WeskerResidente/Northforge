<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class LegalController extends AbstractController
{
    #[Route('/politique-de-confidentialite', name: 'app_privacy')]
    public function privacy(): Response
    {
        return $this->render('legal/page.html.twig', [
            'title' => 'Politique de confidentialité',
            'heading' => 'Politique de confidentialité',
            'intro' => 'Cette page explique comment Northforge traite vos données lorsque vous utilisez la plateforme.',
            'sections' => [
                [
                    'title' => 'Données collectées',
                    'paragraphs' => [
                        'Nous pouvons collecter les informations nécessaires au fonctionnement du compte, à la sécurité du site et à la gestion des demandes.',
                    ],
                ],
                [
                    'title' => 'Utilisation des données',
                    'paragraphs' => [
                        'Les données sont utilisées pour assurer l accès au service, améliorer l expérience utilisateur et répondre aux obligations légales.',
                    ],
                ],
            ],
        ]);
    }

    #[Route('/conditions-d-utilisation', name: 'app_terms')]
    public function terms(): Response
    {
        return $this->render('legal/page.html.twig', [
            'title' => 'Conditions d’utilisation',
            'heading' => 'Conditions d’utilisation',
            'intro' => 'L accès et l utilisation de Northforge impliquent l acceptation des règles suivantes.',
            'sections' => [
                [
                    'title' => 'Accès au service',
                    'paragraphs' => [
                        'Northforge se réserve le droit de modifier ou d interrompre temporairement l accès au site pour maintenance ou évolution.',
                    ],
                ],
                [
                    'title' => 'Responsabilités',
                    'paragraphs' => [
                        'Chaque utilisateur est responsable des informations qu il publie et de l usage qu il fait de la plateforme.',
                    ],
                ],
            ],
        ]);
    }

    #[Route('/politique-de-cookies', name: 'app_cookies')]
    public function cookies(): Response
    {
        return $this->render('legal/page.html.twig', [
            'title' => 'Politique de cookies',
            'heading' => 'Politique de cookies',
            'intro' => 'Northforge utilise des cookies afin d assurer le fonctionnement du site et de mesurer son usage.',
            'sections' => [
                [
                    'title' => 'Cookies techniques',
                    'paragraphs' => [
                        'Ils sont indispensables pour maintenir la session, sécuriser la navigation et conserver certaines préférences.',
                    ],
                ],
                [
                    'title' => 'Gestion des cookies',
                    'paragraphs' => [
                        'Vous pouvez supprimer ou bloquer les cookies depuis les paramètres de votre navigateur.',
                    ],
                    'items' => [
                        'Cookies de session',
                        'Cookies de sécurité',
                        'Cookies de préférences',
                    ],
                ],
            ],
        ]);
    }
}