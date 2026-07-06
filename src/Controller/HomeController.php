<?php

namespace App\Controller;

use App\Repository\ExchangeObjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ExchangeObjectRepository $exchangeObjectRepository): Response
    {
        return $this->render('home/index.html.twig', [
            'featuredObjects' => $exchangeObjectRepository->findFeaturedForHome(),
        ]);
    }
}
