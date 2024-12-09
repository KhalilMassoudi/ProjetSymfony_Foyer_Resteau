<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\DeamndeService;
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
