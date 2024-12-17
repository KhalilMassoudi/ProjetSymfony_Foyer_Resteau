<?php

namespace App\Repository;

use App\Entity\Reservation;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }


    public function findReservationsByDate(\DateTimeInterface $dateDebut, \DateTimeInterface $dateFin)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.dateDebut >= :dateDebut')
            ->andWhere('r.dateFin <= :dateFin')
            ->setParameter('dateDebut', $dateDebut)
            ->setParameter('dateFin', $dateFin)
            ->getQuery()
            ->getResult();
    }


    public function isChambreAvailable(int $chambreId, \DateTimeInterface $dateDebut, \DateTimeInterface $dateFin): bool
    {
        $reservation = $this->createQueryBuilder('r')
            ->andWhere('r.chambre = :chambreId')
            ->andWhere('r.dateDebut < :dateFin')
            ->andWhere('r.dateFin > :dateDebut')
            ->setParameter('chambreId', $chambreId)
            ->setParameter('dateDebut', $dateDebut)
            ->setParameter('dateFin', $dateFin)
            ->getQuery()
            ->getOneOrNullResult();

        return $reservation === null; // Si pas de résultat, la chambre est disponible
    }
    public function countReservations(): int
    {
        return $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    // Réservations par statut (En attente, Acceptée, Rejetée)


    // Réservations groupées par chambre
    public function countReservationsByChambre(): array
    {
        return $this->createQueryBuilder('r')
            ->select('c.numeroChB AS chambre, c.image AS image, COUNT(r.id) AS total')
            ->join('r.chambre', 'c')
            ->groupBy('c.id')
            ->getQuery()
            ->getResult();
    }
    public function countAcceptedReservations(): int
    {
        return $this->createQueryBuilder('r')
            ->select('COUNT(r)')
            ->where('r.statut = :statut')
            ->setParameter('statut', 'Accepté') // Adaptez "Accepté" selon vos statuts définis
            ->getQuery()
            ->getSingleScalarResult(); // Retourne le total comme un entier
    }
    public function countRejectedReservations(): int
    {
        return $this->createQueryBuilder('r')
            ->select('COUNT(r)')
            ->where('r.statut = :statut')
            ->setParameter('statut', 'Rejeté') // Adaptez "Rejeté" selon vos statuts définis
            ->getQuery()
            ->getSingleScalarResult();
    }
    public function countPendingReservations(): int
    {
        return $this->createQueryBuilder('r')
            ->select('COUNT(r)')
            ->where('r.statut = :statut')
            ->setParameter('statut', 'en attente') // Adaptez "en attente" selon vos statuts définis
            ->getQuery()
            ->getSingleScalarResult();
    }
    // Réservations dans une période donnée (exemple par mois)
    public function countReservationsByMonth(\DateTime $start, \DateTime $end): array
    {
        return $this->createQueryBuilder('r')
            ->select('DATE_FORMAT(r.dateReservation, \'%Y-%m\') AS mois, COUNT(r.id) AS total')
            ->where('r.dateReservation BETWEEN :start AND :end')
            ->setParameters([
                'start' => $start,
                'end' => $end,
            ])
            ->groupBy('mois')
            ->getQuery()
            ->getResult();
    }
    public function findPendingReservations(): array
    {
        return $this->createQueryBuilder('r')
            ->where('r.statut = :statut') // Seules les réservations avec le statut "en attente"
            ->setParameter('statut', 'en attente') // Adapter le statut selon vos besoins
            ->orderBy('r.dateCreation', 'DESC') // Optionnel pour trier les notifications par date
            ->getQuery()
            ->getResult();
    }
    public function findByUser(User $user): array
{
    // Create the query builder
    $qb = $this->createQueryBuilder('rs');

    // Define the query to get demandes for the given user
    $qb->andWhere('rs.user = :user')
        ->setParameter('user', $user)
        ->orderBy('rs.id', 'DESC'); // Order by ID or modify as needed

    // Execute the query and return the result
    return $qb->getQuery()->getResult();
}

}