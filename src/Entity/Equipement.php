<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class Equipement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $idEquipementB;

    #[ORM\Column(type: 'string', length: 100)]
    #[Assert\NotBlank(message: "Le nom de l'équipement est obligatoire.")]
    #[Assert\Length(
        max: 100,
        maxMessage: "Le nom de l'équipement ne peut pas dépasser {{ limit }} caractères."
    )]
    #[Assert\Regex(
        pattern: "/^[a-zA-Z0-9 ]*$/",
        message: "Le nom de l'équipement ne doit contenir que des lettres, des chiffres et des espaces."
    )]
    private string $nomEquipementB;

    #[ORM\Column(type: 'string', length: 50)]
    #[Assert\NotBlank(message: "L'état de l'équipement est obligatoire.")]
    #[Assert\Length(
        max: 50,
        maxMessage: "L'état de l'équipement ne peut pas dépasser {{ limit }} caractères."
    )]
    #[Assert\Regex(
        pattern: "/^[a-zA-Z0-9 ]*$/",
        message: "L'état de l'équipement ne doit contenir que des lettres, des chiffres et des espaces."
    )]
    private string $etatEquipementB;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\Column(type: 'date')]
    #[Assert\NotBlank(message: "La date du dernier entretien est obligatoire.")]
    private \DateTimeInterface $dateDernierEntretienEquipementB;

    #[ORM\ManyToOne(inversedBy: 'equipements')]
    private ?Chambre $chambre = null;
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?float $rating = null;

    private ?string $term; // Variable utilisée pour la recherche


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

    public function setChambre(?Chambre $chambre): self
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


    public function getTerm(): ?string
    {
        return $this->term;
    }

    public function setTerm(?string $term): self
    {
        $this->term = $term;
        return $this;
    }
    public function getRating(): ?float
    {
        return $this->rating;
    }

    public function setRating(?float $rating): self
    {
        $this->rating = $rating;
        return $this;
    }
}
