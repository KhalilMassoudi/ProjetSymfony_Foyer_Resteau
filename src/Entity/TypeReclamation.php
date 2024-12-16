<?php

namespace App\Entity;

use App\Repository\TypeReclamationRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TypeReclamationRepository::class)]
class TypeReclamation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom est obligatoire.")]
    #[Assert\Regex(
        pattern: "/^[a-zA-ZÀ-ÿ\s'-]+$/u",
        message: "Le nom ne doit contenir que des lettres, des espaces, des apostrophes ou des tirets."
    )]
    private ?string $nom_typeReclamation = null;

    #[ORM\OneToMany(mappedBy: 'typeReclamations', targetEntity: Reclamation::class)]

    private Collection $reclamations; // On récupère les réclamations associées

    public function __construct()
    {
        $this->reclamations = new ArrayCollection();
    }

    // Getters et setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomTypeReclamation(): ?string
    {
        return $this->nom_typeReclamation;
    }

    public function setNomTypeReclamation(string $nomType): static
    {
        $this->nom_typeReclamation = $nomType;
         return $this;
    }

    public function getReclamations(): Collection
    {
        return $this->reclamations;
    }
}
