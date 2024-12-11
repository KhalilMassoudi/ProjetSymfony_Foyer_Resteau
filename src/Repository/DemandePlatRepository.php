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

    // Vous pouvez ajouter des méthodes personnalisées ici pour interroger la base de données
}
