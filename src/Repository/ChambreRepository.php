<?php

namespace App\Repository;

use App\Entity\Chambre;
use App\Enum\ChambreStatut;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class ChambreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Chambre::class);
    }

    // Méthode pour gérer le téléchargement d'image
    public function handleImageUpload(UploadedFile $imageFile, SluggerInterface $slugger, string $uploadDir): ?string
    {
        $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $slugger->slug($originalFilename);
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

        try {
            $imageFile->move($uploadDir, $newFilename);
            return $newFilename;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function searchAndFilter(array $criteria): array
    {
        $qb = $this->createQueryBuilder('c');

        // Barre de recherche pour le numéro de chambre
        if (!empty($criteria['numeroChB'])) {
            $qb->andWhere('c.numeroChB LIKE :numeroChB')
                ->setParameter('numeroChB', '%' . $criteria['numeroChB'] . '%');
        }

        // Filtrage par étage (intervalle 1 à 10)
        if (!empty($criteria['etage_min']) && !empty($criteria['etage_max'])) {
            $qb->andWhere('c.etageChB BETWEEN :etage_min AND :etage_max')
                ->setParameter('etage_min', $criteria['etage_min'])
                ->setParameter('etage_max', $criteria['etage_max']);
        }

        // Filtrage par capacité (intervalle 1 à 5)
        if (!empty($criteria['capacite_min']) && !empty($criteria['capacite_max'])) {
            $qb->andWhere('c.capaciteChB BETWEEN :capacite_min AND :capacite_max')
                ->setParameter('capacite_min', $criteria['capacite_min'])
                ->setParameter('capacite_max', $criteria['capacite_max']);
        }

        // Filtrage par statut
        if (!empty($criteria['statutChB'])) {
            $qb->andWhere('c.statutChB = :statutChB')
                ->setParameter('statutChB', $criteria['statutChB']);
        }


        // Filtrage par prix (intervalle)
        if (!empty($criteria['prix_min']) && !empty($criteria['prix_max'])) {
            $qb->andWhere('c.prixChB BETWEEN :prix_min AND :prix_max')
                ->setParameter('prix_min', $criteria['prix_min'])
                ->setParameter('prix_max', $criteria['prix_max']);
        }

        return $qb->getQuery()->getResult();
    }
    public function countByStatut(): array
    {
        $qb = $this->createQueryBuilder('c');

        $qb->select('c.statutChB as statut, COUNT(c.id) as total')
            ->where($qb->expr()->in(
                'c.statutChB',
                ':statuts'
            ))
            ->setParameter('statuts', [
                ChambreStatut::disponible(), // Utilise la méthode statique
                ChambreStatut::occupee(),    // Utilise la méthode statique
            ])
            ->groupBy('c.statutChB');

        return $qb->getQuery()->getResult();
    }

}
