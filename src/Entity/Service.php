<?php

namespace App\Entity;

use App\Repository\ServiceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert ;


#[ORM\Entity(repositoryClass: ServiceRepository::class)]
class Service
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom du service est obligatoire")]
    private ?string $nom = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\Date]
    #[Assert\Range(
        min: '2024-01-01',
        max: '2024-12-31',
        notInRangeMessage: 'La date doit être comprise entre {{ min }} et {{ max }}.'
    )]
    private ?\DateTimeInterface $date_creation = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\Date]
    #[Assert\GreaterThan(
        value: 'today',
        message: 'La date doit être après aujourd\'hui.'
    )]
    private ?\DateTimeInterface $date_fin = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(min: 10,  minMessage: "Votre description doit contenir entre 10 et 255 caracteres")]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'services')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(
        message: 'Le type de service est obligatoire.'
    )]
    private ?TypeService $TypeService = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }
    // Getters and Setters
    public function getDateCreation(): ?\DateTime
    {
        return $this->date_creation;
        
    }

    public function setDateCreation(\DateTime $date_creation): self
    {
        $this->date_creation = $date_creation;
        return $this;
    }

    public function getDateFin(): ?\DateTime
    {
        return $this->date_fin;
    }

    public function setDateFin(?\DateTime $date_fin): self
    {
        $this->date_fin = $date_fin;
        return $this;
    }


    
    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getTypeService(): ?TypeService
    {
        return $this->TypeService;
    }

    public function setTypeService(?TypeService $TypeService): static
    {
        $this->TypeService = $TypeService;

        return $this;
    }
}
