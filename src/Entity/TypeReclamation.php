<?php

namespace App\Entity;

use App\Repository\TypeReclamationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TypeReclamationRepository::class)]
class TypeReclamation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom_typeReclamation = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomTypeReclamation(): ?string
    {
        return $this->nom_typeReclamation;
    }

    public function setNomTypeReclamation(string $nom_typeReclamation): static
    {
        $this->nom_typeReclamation = $nom_typeReclamation;

        return $this;
    }
}
