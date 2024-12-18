<?php

namespace App\Repository;

use App\Entity\Plat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Plat>
 */
class PlatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Plat::class);
    }
    public function searchAndFilter(array $searchTerms): array
    {
        $qb = $this->createQueryBuilder('p')
                   ->leftJoin('p.categoriePlat', 'c'); // Jointure avec CategoriePlat

        // Filtrage par nomPlat
        if (!empty($searchTerms['nomPlat'])) {
            $qb->andWhere('p.nomPlat LIKE :nomPlat')
               ->setParameter('nomPlat', '%' . $searchTerms['nomPlat'] . '%');
        }

        // Filtrage par prixMin
        if (!empty($searchTerms['prix_min'])) {
            $qb->andWhere('p.prixPlat >= :prixMin')
               ->setParameter('prixMin', $searchTerms['prix_min']);
        }

        // Filtrage par prixMax
        if (!empty($searchTerms['prix_max'])) {
            $qb->andWhere('p.prixPlat <= :prixMax')
               ->setParameter('prixMax', $searchTerms['prix_max']);
        }

        // Filtrage par catégoriePlat
        if (!empty($searchTerms['categoriePlat'])) {
            $qb->andWhere('c.id = :categoriePlat')
               ->setParameter('categoriePlat', $searchTerms['categoriePlat']);
        }

        return $qb->getQuery()->getResult();
    }
    public function findOutOfStockPlats(): array
{
    return $this->createQueryBuilder('p')
        ->where('p.quantite = 0')
        ->getQuery()
        ->getResult();
}
public function countTotalPlats(): int
{
    return $this->createQueryBuilder('p')
        ->select('COUNT(p.id)')
        ->getQuery()
        ->getSingleScalarResult();
}


public function findRecentlyAddedPlats(): array
{
    return $this->createQueryBuilder('p')
        ->orderBy('p.createdAt', 'DESC')
        ->setMaxResults(5)
        ->getQuery()
        ->getResult();
}
public function findMostRequestedPlats()
    {
        return $this->createQueryBuilder('d')
            ->select('p.nom AS plat_name', 'COUNT(d.id) AS demande_count')
            ->join('d.plat', 'p')
            ->groupBy('p.id')
            ->orderBy('demande_count', 'DESC')
            ->getQuery()
            ->getResult();
    }
//    /**
//     * @return Plat[] Returns an array of Plat objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Plat
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
