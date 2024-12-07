<?php

namespace App\Repository;

use App\Entity\Chambre;
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

    public function findByTerm(array $searchTerms)
    {
        $qb = $this->createQueryBuilder('c');

        // Recherche par numéro de chambre
        if (!empty($searchTerms['numeroChB'])) {
            $qb->andWhere('c.numeroChB LIKE :numeroChB')
                ->setParameter('numeroChB', '%' . $searchTerms['numeroChB'] . '%');
        }

        // Recherche par étage
        if (!empty($searchTerms['etageChB'])) {
            $qb->andWhere('c.etageChB = :etageChB')
                ->setParameter('etageChB', $searchTerms['etageChB']);
        }

        // Recherche par capacité
        if (!empty($searchTerms['capaciteChB'])) {
            $qb->andWhere('c.capaciteChB = :capaciteChB')
                ->setParameter('capaciteChB', $searchTerms['capaciteChB']);
        }

        // Recherche par statut
        if (!empty($searchTerms['statutChB'])) {
            $qb->andWhere('c.statutChB = :statutChB')
                ->setParameter('statutChB', $searchTerms['statutChB']);
        }

        // Recherche par prix exact
        if (!empty($searchTerms['prixChB'])) {
            $qb->andWhere('c.prixChB = :prixChB')
                ->setParameter('prixChB', $searchTerms['prixChB']);
        }

        return $qb->getQuery()->getResult();
    }



}
