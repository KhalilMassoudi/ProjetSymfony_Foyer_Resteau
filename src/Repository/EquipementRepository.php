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
    // EquipementRepository.php

    public function findByTerm(array $criteria): array
    {
        $queryBuilder = $this->createQueryBuilder('e');

        if (!empty($criteria['nomEquipementB'])) {
            $queryBuilder->andWhere('LOWER(e.nomEquipementB) LIKE :nomEquipementB')
                ->setParameter('nomEquipementB', '%' . strtolower($criteria['nomEquipementB']) . '%');
        }


        if (!empty($criteria['etatEquipementB'])) {
            $queryBuilder->andWhere('LOWER(e.etatEquipementB) LIKE :etatEquipementB')
                ->setParameter('etatEquipementB', '%' . strtolower($criteria['etatEquipementB']) . '%');
        }


        if (!empty($criteria['numeroChB'])) {
            $queryBuilder->innerJoin('e.chambre', 'c')
                ->andWhere('LOWER(c.numeroChB) LIKE :numeroChB')
                ->setParameter('numeroChB', '%' . strtolower($criteria['numeroChB']) . '%');
        }

        return $queryBuilder->getQuery()->getResult();
    }



}
