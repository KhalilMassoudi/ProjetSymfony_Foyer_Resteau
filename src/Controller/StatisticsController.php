<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\ReservationRepository;
use App\Repository\ChambreRepository;
use Symfony\Component\Routing\Annotation\Route;

class StatisticsController extends AbstractController
{
    #[Route('/back/statistiques', name: 'app_statistics')]
    public function statistiques(
        ChambreRepository $chambreRepository,
        ReservationRepository $reservationRepository
    ): Response {
        // Récupération des statistiques pour les chambres
        $roomStats = $chambreRepository->countByStatut();

        // Formater les statistiques des chambres pour le graphique
        $formattedData = array_map(static function ($stat) {
            return [
                'statut' => $stat['statut'], // Exemple : "Disponible" ou "Occupé"
                'value' => $stat['total'],  // Exemple : 5 ou 10
            ];
        }, $roomStats);

        // Récupération des statistiques des réservations
        $totalReservations = $reservationRepository->countReservations();
        $acceptedReservations = $reservationRepository->countAcceptedReservations();
        $rejectedReservations = $reservationRepository->countRejectedReservations();
        $pendingReservations = $reservationRepository->countPendingReservations();
        $reservationsByChambre = $reservationRepository->countReservationsByChambre();

        // Rendu de la vue avec les données nécessaires
        return $this->render('backtemplates/chrv.html.twig', [
            'data' => $formattedData, // Données des statistiques des chambres
            'totalReservations' => $totalReservations,
            'acceptedReservations' => $acceptedReservations,
            'rejectedReservations' => $rejectedReservations,
            'pendingReservations' => $pendingReservations,
            'reservationsByChambre' => $reservationsByChambre,
        ]);
    }
}