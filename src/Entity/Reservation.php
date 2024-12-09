<?php
// src/Entity/Reservation.php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Chambre::class, inversedBy: 'reservations', cascade: ['remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private Chambre $chambre;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $dateArrivee;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $dateDepart;

    #[ORM\Column(type: 'string', length: 255)]
    private string $nomEtudiant;

    #[ORM\Column(type: 'string', length: 255)]
    private string $emailEtudiant;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $telephoneEtudiant = null;

    public function getId(): int
    {
        return $this->id;
    }



    public function getChambre(): Chambre
    {
        return $this->chambre;
    }

    public function setChambre(Chambre $chambre): self
    {
        $this->chambre = $chambre;
        return $this;
    }

    public function getDateArrivee(): \DateTimeInterface
    {
        return $this->dateArrivee;
    }

    public function setDateArrivee(\DateTimeInterface $dateArrivee): self
    {
        $this->dateArrivee = $dateArrivee;
        return $this;
    }

    public function getDateDepart(): \DateTimeInterface
    {
        return $this->dateDepart;
    }

    public function setDateDepart(\DateTimeInterface $dateDepart): self
    {
        $this->dateDepart = $dateDepart;
        return $this;
    }

    public function getNomEtudiant(): string
    {
        return $this->nomEtudiant;
    }

    public function setNomEtudiant(string $nomEtudiant): self
    {
        $this->nomEtudiant = $nomEtudiant;
        return $this;
    }

    public function getEmailEtudiant(): string
    {
        return $this->emailEtudiant;
    }

    public function setEmailEtudiant(string $emailEtudiant): self
    {
        $this->emailEtudiant = $emailEtudiant;
        return $this;
    }

    public function getTelephoneEtudiant(): ?string
    {
        return $this->telephoneEtudiant;
    }

    public function setTelephoneEtudiant(?string $telephoneEtudiant): self
    {
        $this->telephoneEtudiant = $telephoneEtudiant;
        return $this;
    }
}
