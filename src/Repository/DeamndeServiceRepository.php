<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\DemandeService;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<DeamndeService>
 */
class DeamndeServiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DemandeService::class);
    }
      /**
     * Get all demandes for a specific user
     *
     * @param User $user
     * @return DemandeService[] Returns an array of DemandeService objects
     */
    public function findByUser(User $user): array
    {
        // Create the query builder
        $qb = $this->createQueryBuilder('ds');

        // Define the query to get demandes for the given user
        $qb->andWhere('ds.user = :user')
           ->setParameter('user', $user)
           ->orderBy('ds.id', 'DESC'); // Order by ID or modify as needed

        // Execute the query and return the result
        return $qb->getQuery()->getResult();
    }
    
    public function findByStatus($value){
        $query = $this->getEntityManager()

        ->createQuery('SELECT d FROM App\Entity\DemandeService d WHERE d.status = :value')->setParameter('value', $value);  

        return $query->getResult();
    }
    /**
     * Search and filter demands based on criteria
     *
     * @param array $criteria
     * @return DemandeService[]
     */
    public function searchAndFilter(array $criteria): array
    {
        $qb = $this->createQueryBuilder('d');

        // Filter by status
        if (!empty($criteria['status'])) {
            $qb->andWhere('d.status = :status')
                ->setParameter('status', $criteria['status']);
        }

        // Filter by service
        if (!empty($criteria['service'])) {
            $qb->andWhere('d.service = :service')
                ->setParameter('service', $criteria['service']);
        }

        // Filter by date range
        if (!empty($criteria['date_min'])) {
            $qb->andWhere('d.date_demande >= :date_min')
                ->setParameter('date_min', $criteria['date_min']);
        }
        if (!empty($criteria['date_max'])) {
            $qb->andWhere('d.date_demande <= :date_max')
                ->setParameter('date_max', $criteria['date_max']);
        }

        // Filter by user
        if (!empty($criteria['user'])) {
            $qb->andWhere('d.user = :user')
                ->setParameter('user', $criteria['user']);
        }

        return $qb->getQuery()->getResult();
    }

    //    /**
    //     * @return DeamndeService[] Returns an array of DeamndeService objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('d.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?DeamndeService
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
