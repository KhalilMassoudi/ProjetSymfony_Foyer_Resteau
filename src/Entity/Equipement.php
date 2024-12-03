<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Equipement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $idEquipementB;

    #[ORM\Column(type: 'string', length: 100)]
    private string $nomEquipementB;

    #[ORM\Column(type: 'string', length: 50)]
    private string $etatEquipementB;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $image = null;
    #[ORM\Column(type: 'date')]
    private \DateTimeInterface $dateDernierEntretienEquipementB;

    #[ORM\ManyToOne(inversedBy: 'equipements')]
    private ?Chambre $chambre = null;

    // Getters and Setters

    public function getIdEquipementB(): int
    {
        return $this->idEquipementB;
    }

    public function setIdEquipementB(int $idEquipementB): self
    {
        $this->idEquipementB = $idEquipementB;

        return $this;
    }

    public function getNomEquipementB(): string
    {
        return $this->nomEquipementB;
    }

    public function setNomEquipementB(string $nomEquipementB): self
    {
        $this->nomEquipementB = $nomEquipementB;

        return $this;
    }

    public function getEtatEquipementB(): string
    {
        return $this->etatEquipementB;
    }

    public function setEtatEquipementB(string $etatEquipementB): self
    {
        $this->etatEquipementB = $etatEquipementB;

        return $this;
    }

    public function getDateDernierEntretienEquipementB(): \DateTimeInterface
    {
        return $this->dateDernierEntretienEquipementB;
    }

    public function setDateDernierEntretienEquipementB(\DateTimeInterface $dateDernierEntretienEquipementB): self
    {
        $this->dateDernierEntretienEquipementB = $dateDernierEntretienEquipementB;

        return $this;
    }

    public function getChambre(): ?Chambre
    {
        return $this->chambre;
    }

    public function setChambre(?Chambre $chambre): static
    {
        $this->chambre = $chambre;

        return $this;
    }
    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;
        return $this;
    }
}
