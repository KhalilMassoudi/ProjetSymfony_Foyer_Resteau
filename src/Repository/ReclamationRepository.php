<?php

namespace App\Repository;

use App\Entity\Reclamation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reclamation>
 */
class ReclamationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reclamation::class);
    }
    public function respondedanfindByUser($userId)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.user = :userId')
            ->andWhere('r.etat = :etat')
            ->setParameter('userId', $userId)
            ->setParameter('etat', 'répondue')
            ->orderBy('r.date_Reclamation', 'DESC') // Trier par date (optionnel)
            ->getQuery()
            ->getResult();
    }

    public function countTotalReclamations(): int
    {
        return $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }
    public function countReclamationsRepondues(): int
    {
        return $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->where('r.etat = :etat')
            ->setParameter('etat', 'répondue')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countFeedbacks(): int
    {
        $qb = $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->where('r.rating != :rating')
            ->setParameter('rating', 0);

        return (int) $qb->getQuery()->getSingleScalarResult();
    }
    public function countPositiveRatings(): int
    {
        $queryBuilder = $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->where('r.rating IN (:positiveRatings)')
            ->andWhere('r.rating != 0') // Exclure les réclamations sans note
            ->setParameter('positiveRatings', [3, 4, 5]);

        return (int) $queryBuilder->getQuery()->getSingleScalarResult();
    }
    public function countNegativeRatings(): int
    {
        $queryBuilder = $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->where('r.rating IN (:negativeRatings)')
            ->andWhere('r.rating != 0') // Exclure les réclamations sans note
            ->setParameter('negativeRatings', [1, 2]);

        return (int) $queryBuilder->getQuery()->getSingleScalarResult();
    }
    //    /**
    //     * @return Reclamation[] Returns an array of Reclamation objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Reclamation
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    public function findReclamationsRepondue(): array
    {
        $qb = $this->createQueryBuilder('r')
            ->where('r.etat = :etat')
            ->setParameter('etat', 'répondue');

        return $qb->getQuery()->getResult();
    }
    public function findReclamationsSuspendue(): array
    {
        $qb = $this->createQueryBuilder('r')
            ->where('r.etat = :etat')
            ->setParameter('etat', 'suspendue');

        return $qb->getQuery()->getResult();
    }
    public function findTypesAndCounts(): array
    {
        $qb = $this->createQueryBuilder('r')
            ->select('t.nom_typeReclamation as typeName, COUNT(r.id) as count') // Remplacez "nom" par le champ représentant le nom du type dans votre entité TypeReclamation
            ->join('r.typeReclamations', 't') // Liaison avec l'entité TypeReclamation
            ->groupBy('t.id') // Grouper par l'ID du type pour éviter les doublons.

            ->orderBy('count', 'DESC'); // Optionnel : Trier par le nombre décroissant.

        $results = $qb->getQuery()->getResult();

        // Transformer en tableau clé-valeur
        $typesAndCounts = [];
        foreach ($results as $result) {
            $typesAndCounts[$result['typeName']] = (int) $result['count']; // Convertir le count en entier
        }

        return $typesAndCounts;
    }

    public function advancedSearch(array $criteria): array
    {
        $qb = $this->createQueryBuilder('r')
            ->leftJoin('r.user', 'u')
            ->addSelect('u');

        // Ajout des critères dynamiques
        if (!empty($criteria['titre'])) {
            $qb->andWhere('r.titre LIKE :titre')
                ->setParameter('titre', '%' . $criteria['titre'] . '%');
        }

        if (!empty($criteria['nomEtudiant'])) {
            $qb->andWhere('u.username LIKE :nomEtudiant')
                ->setParameter('nomEtudiant', '%' . $criteria['nomEtudiant'] . '%');
        }

        if (!empty($criteria['email'])) {
            $qb->andWhere('u.email LIKE :email')
                ->setParameter('email', '%' . $criteria['email'] . '%');
        }

        if (!empty($criteria['dateReclamation'])) {
            $qb->andWhere('r.date_Reclamation = :dateReclamation')
                ->setParameter('dateReclamation', $criteria['dateReclamation']);
        }

        return $qb->getQuery()->getResult();
    }
    public function findFavorisByUser($userId): array
    {
        return $this->createQueryBuilder('r')
            ->where('r.favori = :favori')
            ->andWhere('r.user = :userId')  // Assurez-vous que la relation entre Reclamation et User est bien configurée
            ->setParameter('favori', true)
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
    }
    public function countFavorisByUser($userId): int
    {
        return $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')  // Compter les réclamations favorisées
            ->where('r.favori = :favori')  // Seulement les favoris
            ->andWhere('r.user = :userId')   // Filtrer par l'utilisateur
            ->setParameter('favori', true)
            ->setParameter('userId', $userId)  // Assurez-vous que l'ID utilisateur est bien passé
            ->getQuery()
            ->getSingleScalarResult();  // Retourne un seul résultat (le nombre de favoris)
    }

}
