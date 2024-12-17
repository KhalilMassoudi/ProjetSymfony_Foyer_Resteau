<?php

namespace App\Repository;

use App\Entity\Service;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Service>
 */
class ServiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Service::class);
    }
    public function findServiceByNameOrType(string $term): array
    {
        return $this->createQueryBuilder('s')
            ->leftJoin('s.TypeService', 't')
            ->addSelect('t')
            ->where('s.nom LIKE :term OR t.nom LIKE :term')
            ->setParameter('term', '%' . $term . '%')
            ->getQuery()
            ->getResult();
    }
    /**
     * Get the service statistics (number of users by service)
     */
    public function getServiceStatistics(): array
    {
        return $this->createQueryBuilder('s')
            ->select('s.nom AS service', 'COUNT(DISTINCT u.id) AS user_count')
            ->leftJoin('s.demandes', 'd')
            ->leftJoin('d.user', 'u')
            ->where('d.status = :accepted') // Only count accepted demands
            ->setParameter('accepted', 'accepted') // Adjust according to the accepted value
            ->groupBy('s.id')
            ->getQuery()
            ->getResult();
    }
    
    public function getUsersInscrits(): int
{
    return $this->getEntityManager()->createQuery(
        'SELECT COUNT(DISTINCT u.id)
         FROM App\Entity\User u
         JOIN App\Entity\DemandeService d WITH d.user = u
         WHERE d.status = :acceptedStatus'  // Assuming there is a 'status' field indicating acceptance
    )
    ->setParameter('acceptedStatus', 'accepted')  // Adjust according to your actual accepted status
    ->getSingleScalarResult();
}

    /**
     * Get the total number of users
     */
    public function getTotalUsers(): int
    {
        return $this->getEntityManager()->createQuery(
            'SELECT COUNT(u.id) FROM App\Entity\User u'
        )->getSingleScalarResult();
    }


    //    /**
    //     * @return Service[] Returns an array of Service objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Service
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}