<?php

namespace App\Entity;

use App\Enum\ChambreStatut;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Chambre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 50)]
    private string $numeroChB;

    #[ORM\Column(type: 'integer')]
    private int $etageChB;

    #[ORM\Column(type: 'integer')]
    private int $capaciteChB;

    #[ORM\Column(type: 'string', length: 20)]
    private string $statutChB;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\Column(type: 'float')]
    private float $prixChB;

    /**
     * @var Collection<int, Equipement>
     */
    #[ORM\OneToMany(targetEntity: Equipement::class, mappedBy: 'chambre', cascade: ['remove'])]
    private Collection $equipements;

    public function __construct()
    {
        $this->equipements = new ArrayCollection();
    }  // Stocker la valeur de l'énumération sous forme de chaîne

    // Getter pour id
    public function getId(): int
    {
        return $this->id;
    }

    // Getter et Setter pour numeroChB
    public function getNumeroChB(): string
    {
        return $this->numeroChB;
    }

    public function setNumeroChB(string $numeroChB): self
    {
        $this->numeroChB = $numeroChB;
        return $this;
    }

    // Getter et Setter pour etageChB
    public function getEtageChB(): int
    {
        return $this->etageChB;
    }

    public function setEtageChB(int $etageChB): self
    {
        $this->etageChB = $etageChB;
        return $this;
    }

    // Getter et Setter pour capaciteChB
    public function getCapaciteChB(): int
    {
        return $this->capaciteChB;
    }

    public function setCapaciteChB(int $capaciteChB): self
    {
        $this->capaciteChB = $capaciteChB;
        return $this;
    }

    // Getter et Setter pour statutChB
    public function getStatutChB(): ChambreStatut
    {
        // Retourner l'énumération en la convertissant depuis la chaîne stockée
        return ChambreStatut::from($this->statutChB);
    }

    public function setStatutChB(ChambreStatut $statut): self
    {
        // Stocker la valeur de l'énumération sous forme de chaîne
        $this->statutChB = $statut->value;
        return $this;
    }

    /**
     * @return Collection<int, Equipement>
     */
    public function getEquipements(): Collection
    {
        return $this->equipements;
    }

    public function addEquipement(Equipement $equipement): static
    {
        if (!$this->equipements->contains($equipement)) {
            $this->equipements->add($equipement);
            $equipement->setChambre($this);
        }

        return $this;
    }

    public function removeEquipement(Equipement $equipement): static
    {
        if ($this->equipements->removeElement($equipement)) {
            // set the owning side to null (unless already changed)
            if ($equipement->getChambre() === $this) {
                $equipement->setChambre(null);
            }
        }

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
}