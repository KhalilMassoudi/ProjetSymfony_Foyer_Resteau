<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\ReservationRepository;
use App\Repository\ChambreRepository;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\EquipementRepository;
class StatisticsController extends AbstractController
{
    #[Route('/back/statistiques', name: 'app_statistics', methods: ['GET'])]
    public function statistiques(
        ChambreRepository $chambreRepository,
        ReservationRepository $reservationRepository,
        EquipementRepository $equipementRepository
    ): Response {
        // Récupération de la moyenne des notes des équipements
        $averageRating = $equipementRepository->getAverageRating();

        // Récupération des statistiques pour les chambres
        $roomStats = $chambreRepository->countByStatut();

        // Gestion des données formatées et défaut si vide
        $defaultFormattedData = [
            ['statut' => 'Pas de données', 'value' => 0],
        ];
        $formattedData = !empty($roomStats) ? array_map(static function ($stat) {
            return [
                'statut' => $stat['statut'], // Exemple : "Disponible" ou "Occupé"
                'value' => $stat['total'],  // Exemple : 5 ou 10
            ];
        }, $roomStats) : $defaultFormattedData;

        // Récupération des statistiques des réservations
        $totalReservations = $reservationRepository->countReservations() ?? 0;
        $acceptedReservations = $reservationRepository->countAcceptedReservations() ?? 0;
        $rejectedReservations = $reservationRepository->countRejectedReservations() ?? 0;
        $pendingReservations = $reservationRepository->countPendingReservations() ?? 0;
        $reservationsByChambre = $reservationRepository->countReservationsByChambre() ?? [];

        // Rendu de la vue avec les données nécessaires
        return $this->render('backtemplates/chrv.html.twig', [
            'data' => $formattedData, // Données des statistiques des chambres
            'totalReservations' => $totalReservations,
            'acceptedReservations' => $acceptedReservations,
            'rejectedReservations' => $rejectedReservations,
            'pendingReservations' => $pendingReservations,
            'reservationsByChambre' => $reservationsByChambre,
            'averageRating' => $averageRating, // Ajout de la moyenne des notes des équipements
        ]);
    }
}