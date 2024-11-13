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
    private ChambreStatut $statutChB;

    // Getters and Setters

    public function getIdChB(): int
    {
        return $this->idChB;
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

    public function getStatutChB(): string
    {
        return $this->statutChB;
    }

    public function setStatutChB(ChambreStatut $statutChB): self
    {
        $this->statutChB = $statutChB;
        return $this;
    }
}
