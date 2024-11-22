<?php

namespace App\Entity;

use App\Repository\PlatRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlatRepository::class)]
class Plat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nomPlat = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $descPlat = null;

    #[ORM\Column]
    private ?float $prixPlat = null;

    #[ORM\Column(length: 255)]
    private ?string $typeCuisine = null; // Changed to a plain string
    
    #[ORM\Column]
    private bool $dispoPlat = true; // New boolean field with a default value of `true`

    #[ORM\ManyToOne(targetEntity: CategoriePlat::class, inversedBy: 'plats')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?CategoriePlat $categoriePlat = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomPlat(): ?string
    {
        return $this->nomPlat;
    }

    public function setNomPlat(string $nomPlat): static
    {
        $this->nomPlat = $nomPlat;
        return $this;
    }

    public function getDescPlat(): ?string
    {
        return $this->descPlat;
    }

    public function setDescPlat(?string $descPlat): static
    {
        $this->descPlat = $descPlat;
        return $this;
    }

    public function getPrixPlat(): ?float
    {
        return $this->prixPlat;
    }

    public function setPrixPlat(float $prixPlat): static
    {
        $this->prixPlat = $prixPlat;
        return $this;
    }

    public function getTypeCuisine(): ?string
    {
        return $this->typeCuisine;
    }

    public function setTypeCuisine(string $typeCuisine): static
    {
        $this->typeCuisine = $typeCuisine;
        return $this;
    }

    public function getCategoriePlat(): ?CategoriePlat
    {
        return $this->categoriePlat;
    }

    public function setCategoriePlat(?CategoriePlat $categoriePlat): static
    {
        $this->categoriePlat = $categoriePlat;
        return $this;
    }

    public function getDispoPlat(): bool
    {
        return $this->dispoPlat;
    }

    public function setDispoPlat(bool $dispoPlat): static
    {
        $this->dispoPlat = $dispoPlat;
        return $this;
    }
}
