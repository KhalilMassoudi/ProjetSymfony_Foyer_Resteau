<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class TypeDeChambre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $idTypeChB;

    #[ORM\Column(type: 'string', length: 50)]
    private string $typeChambreB;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $descriptionChambreB = null;

    #[ORM\Column(type: 'float')]
    private float $prixChambreB;

    // Getters and Setters

    public function getIdTypeChB(): int
    {
        return $this->idTypeChB;
    }

    public function getTypeChambreB(): string
    {
        return $this->typeChambreB;
    }

    public function setTypeChambreB(string $typeChambreB): self
    {
        $this->typeChambreB = $typeChambreB;
        return $this;
    }

    public function getDescriptionChambreB(): ?string
    {
        return $this->descriptionChambreB;
    }

    public function setDescriptionChambreB(?string $descriptionChambreB): self
    {
        $this->descriptionChambreB = $descriptionChambreB;
        return $this;
    }

    public function getPrixChambreB(): float
    {
        return $this->prixChambreB;
    }

    public function setPrixChambreB(float $prixChambreB): self
    {
        $this->prixChambreB = $prixChambreB;
        return $this;
    }
}
