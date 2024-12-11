<?php

namespace App\Entity;

use App\Enum\ChambreStatut;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Chambre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 50)]
    #[Assert\NotBlank(message: "Le numéro de la chambre est obligatoire.")]
    #[Assert\Regex(
        pattern: '/^[A-Z]/',
        message: 'Le numéro de chambre doit commencer par une lettre majuscule.'
    )]
    private string $numeroChB;

    #[ORM\Column(type: 'integer')]
    #[Assert\NotBlank(message: "L'étage de la chambre est obligatoire.")]
    #[Assert\Range(
        min: 1,
        max: 10,
        notInRangeMessage: 'L\'étage doit être compris entre {{ min }} et {{ max }}.'
    )]
    private int $etageChB;

    #[ORM\Column(type: 'integer')]
    #[Assert\NotBlank(message: "La capacité de la chambre est obligatoire.")]
    #[Assert\Range(
        min: 1,
        max: 4,
        notInRangeMessage: 'La capacité doit être comprise entre {{ min }} et {{ max }}.'
    )]
    private int $capaciteChB;

    #[ORM\Column(type: 'string', length: 20)]
    #[Assert\NotBlank(message: "Le statut de la chambre est obligatoire.")]
    private string $statutChB;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\Column(type: 'float')]
    #[Assert\NotBlank(message: "Le prix de la chambre est obligatoire.")]
    #[Assert\Positive(message: "Le prix de la chambre doit être un nombre positif.")]
    private float $prixChB;

    #[ORM\OneToMany(mappedBy: 'chambre', targetEntity: Reservation::class, cascade: ['remove'])]
    private Collection $reservations;

    #[ORM\OneToMany(targetEntity: Equipement::class, mappedBy: 'chambre', cascade: ['remove'])]
    private Collection $equipements;

    public function __construct()
    {
        $this->reservations = new ArrayCollection();
        $this->equipements = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getNumeroChB(): string
    {
        return $this->numeroChB;
    }

    public function setNumeroChB(string $numeroChB): self
    {
        $this->numeroChB = $numeroChB;
        return $this;
    }

    public function getEtageChB(): int
    {
        return $this->etageChB;
    }

    public function setEtageChB(int $etageChB): self
    {
        $this->etageChB = $etageChB;
        return $this;
    }

    public function getCapaciteChB(): int
    {
        return $this->capaciteChB;
    }

    public function setCapaciteChB(int $capaciteChB): self
    {
        $this->capaciteChB = $capaciteChB;
        return $this;
    }

    public function getStatutChB(): ChambreStatut
    {
        return ChambreStatut::from($this->statutChB);
    }

    public function setStatutChB(ChambreStatut $statut): self
    {
        $this->statutChB = $statut->value;
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

    public function getPrixChB(): float
    {
        return $this->prixChB;
    }

    public function setPrixChB(float $prixChB): self
    {
        $this->prixChB = $prixChB;
        return $this;
    }

    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): self
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations[] = $reservation;
            $reservation->setChambre($this);
        }
        return $this;
    }

    public function removeReservation(Reservation $reservation): self
    {
        $this->reservations->removeElement($reservation);
        return $this;
    }

    public function getEquipements(): Collection
    {
        return $this->equipements;
    }

    public function addEquipement(Equipement $equipement): self
    {
        if (!$this->equipements->contains($equipement)) {
            $this->equipements->add($equipement);
            $equipement->setChambre($this);
        }

        return $this;
    }

    public function removeEquipement(Equipement $equipement): self
    {
        if ($this->equipements->removeElement($equipement)) {
            if ($equipement->getChambre() === $this) {
                $equipement->setChambre(null);
            }
        }

        return $this;
    }
}