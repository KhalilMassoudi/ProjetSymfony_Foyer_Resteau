<?php

namespace App\Repository;

use App\Entity\Equipement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class EquipementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Equipement::class);
    }
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
        $queryBuilder = $this->createQueryBuilder('e')
            ->leftJoin('e.chambre', 'c');  // Join avec Chambre pour numéro de chambre

        // Si un terme de recherche est fourni
        if (!empty($searchTerms['searchTerm'])) {
            $searchWords = explode(' ', $searchTerms['searchTerm']);  // Divisez les termes de recherche

            // On va appliquer le filtre sur chaque terme de recherche dans tous les champs
            foreach ($searchWords as $word) {
                $queryBuilder->andWhere('
                e.nomEquipementB LIKE :word OR 
                e.etatEquipementB LIKE :word OR 
                c.numeroChB LIKE :word')
                    ->setParameter('word', '%' . $word . '%');
            }
        }

        // Exécution de la requête et retour des résultats
        return $queryBuilder->getQuery()->getResult();
    }
    public function save(Equipement $equipement, bool $flush = false): void
    {
        $this->getEntityManager()->persist($equipement);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    public function getAverageRating(): ?float
    {
        $qb = $this->createQueryBuilder('e');

        $qb->select('AVG(e.rating) as avgRating')
            ->where('e.rating IS NOT NULL'); // Ignorer les enregistrements avec NULL

        $result = $qb->getQuery()->getSingleScalarResult();

        return $result ? (float) $result : null; // Retourner null si aucun rating
    }

}
