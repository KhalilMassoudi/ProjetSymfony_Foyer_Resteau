<?php

namespace App\Repository;

use App\Entity\DemandePlat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DemandePlatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DemandePlat::class);
    }
    public function findMostRequestedPlats(): array
    {
        return $this->createQueryBuilder('d')
            ->select('p.nomPlat AS plat_name, COUNT(d.id) AS demande_count')
            ->join('d.plat', 'p')
            ->groupBy('p.id')
            ->orderBy('demande_count', 'DESC')
            ->getQuery()
            ->getResult();
    }
    public function countDemandesByUser(): array
{
    return $this->createQueryBuilder('d')
        ->select('u.email AS user_email, COUNT(d.id) AS demande_count')
        ->join('d.user', 'u')
        ->groupBy('u.id')
        ->orderBy('demande_count', 'DESC')
        ->getQuery()
        ->getResult();
}
public function countTodayDemandes(): int
{
    $count = $this->createQueryBuilder('d')
        ->where('d.createdAt >= :startOfDay')
        ->setParameter('startOfDay', new \DateTime('today'))  // Début d'aujourd'hui
        ->select('COUNT(d.id)')
        ->getQuery()
        ->getSingleScalarResult();

    // Retourner 1 si aucune demande n'a été effectuée aujourd'hui
    return $count > 0 ? $count : 1;
}

public function countThisWeekDemandes(): int
{
    $count = $this->createQueryBuilder('d')
        ->where('d.createdAt >= :startOfWeek')
        ->setParameter('startOfWeek', (new \DateTime())->modify('monday this week'))  // Lundi de cette semaine
        ->select('COUNT(d.id)')
        ->getQuery()
        ->getSingleScalarResult();

    // Retourner 1 si aucune demande n'a été effectuée cette semaine
    return $count > 0 ? $count : 1;
}


public function countThisMonthDemandes(): int
{
    $count = $this->createQueryBuilder('d')
        ->where('d.createdAt >= :startOfMonth')
        ->setParameter('startOfMonth', (new \DateTime('first day of this month')))  // Premier jour du mois
        ->select('COUNT(d.id)')
        ->getQuery()
        ->getSingleScalarResult();
    
    // Si le nombre est 0, on retourne 1 pour éviter la division par zéro
    return $count > 0 ? $count : 1;
}

public function countDemandesByStatus(): array
{
    return $this->createQueryBuilder('d')
        ->select('d.status, COUNT(d.id) AS demande_count')
        ->groupBy('d.status')
        ->getQuery()
        ->getResult();
}

public function findTopActiveUsers(): array
{
    return $this->createQueryBuilder('d')
        ->select('u.email AS user_email, COUNT(d.id) AS demande_count')
        ->join('d.user', 'u')
        ->groupBy('u.id')
        ->orderBy('demande_count', 'DESC')
        ->setMaxResults(5)
        ->getQuery()
        ->getResult();
}


    // Vous pouvez ajouter des méthodes personnalisées ici pour interroger la base de données
}
