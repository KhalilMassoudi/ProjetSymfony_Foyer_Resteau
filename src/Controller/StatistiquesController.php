<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\PlatRepository;
use App\Repository\DemandePlatRepository;
use App\Repository\UserRepository;

class StatistiquesController extends AbstractController
{
    #[Route('/plats/statistiques', name: 'plats_statistiques')]
    public function index(
        PlatRepository $platRepository,
        DemandePlatRepository $demandePlatRepository,
        UserRepository $userRepository
    ): Response {
        // Statistiques sur les plats
        $mostRequestedPlats = $demandePlatRepository->findMostRequestedPlats(); // Plats les plus demandés
        $outOfStockPlats = $platRepository->findOutOfStockPlats(); // Plats en rupture de stock
        $totalPlats = $platRepository->countTotalPlats(); // Nombre total de plats disponibles
        $recentlyAddedPlats = $platRepository->findRecentlyAddedPlats(); // Plats récemment ajoutés

        // Statistiques sur les demandes
        $demandesToday = $demandePlatRepository->countTodayDemandes(); // Demandes aujourd'hui
        $demandesThisWeek = $demandePlatRepository->countThisWeekDemandes(); // Demandes cette semaine
        $demandesThisMonth = $demandePlatRepository->countThisMonthDemandes(); // Demandes ce mois
        $demandesStatus = $demandePlatRepository->countDemandesByStatus(); // Demandes par statut

        // Statistiques sur les utilisateurs
        $topActiveUsers = $demandePlatRepository->findTopActiveUsers(); // Utilisateurs les plus actifs
        $totalUsers = $userRepository->countTotalUsers(); // Nombre total d'utilisateurs

        return $this->render('backtemplates/statgestionrestau.html.twig', [
            'most_requested_plats' => $mostRequestedPlats,
            'out_of_stock_plats' => $outOfStockPlats,
            'total_plats' => $totalPlats,
            'recently_added_plats' => $recentlyAddedPlats,
            'demandes_today' => $demandesToday,
            'demandes_this_week' => $demandesThisWeek,
            'demandes_this_month' => $demandesThisMonth,
            'demandes_status' => $demandesStatus,
            'top_active_users' => $topActiveUsers,
            'total_users' => $totalUsers,
        ]);
    }
}
