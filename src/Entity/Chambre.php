<?php

namespace App\Entity;

use App\Enum\ChambreStatut;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Chambre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $idChB;

    #[ORM\Column(type: 'string', length: 50)]
    private string $numeroChB;

    #[ORM\Column(type: 'integer')]
    private int $etageChB;

    #[ORM\Column(type: 'integer')]
    private int $capaciteChB;

    #[ORM\Column(type: 'string', length: 20)]
    private string $statutChB;  // Stocker la valeur de l'énumération sous forme de chaîne

    // Getter pour idChB
    public function getIdChB(): int
    {
        return $this->idChB;
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
}