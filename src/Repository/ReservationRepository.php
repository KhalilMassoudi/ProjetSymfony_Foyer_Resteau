<?php

namespace App\Repository;

use App\Entity\Reservation;
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
}