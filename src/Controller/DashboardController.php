<?php

namespace App\Controller;

use App\Repository\CategorieRepository;
use App\Repository\CommandeRepository;
use App\Repository\ProduitRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_dashboard')]
    public function index(
        CategorieRepository $categorieRepository,
        ProduitRepository $produitRepository,
        CommandeRepository $commandeRepository,
        UserRepository $userRepository
    ): Response {
        $stats = [
            'categories' => $categorieRepository->count([]),
            'produits' => $produitRepository->count([]),
            'commandes' => $commandeRepository->count([]),
            'users' => $userRepository->count([]),
        ];

        $recentCommandes = $commandeRepository->findRecentCommandes(5);

        return $this->render('dashboard/index.html.twig', [
            'stats' => $stats,
            'recent_commandes' => $recentCommandes,
        ]);
    }
}