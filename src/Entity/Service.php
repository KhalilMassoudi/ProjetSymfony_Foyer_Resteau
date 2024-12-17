<?php

namespace App\Entity;

use App\Repository\ServiceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
    private ?\DateTimeInterface $date_creation = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(min: 10,  minMessage: "Votre description doit contenir entre 10 et 255 caracteres")]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'services')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(
        message: 'Le type de service est obligatoire.'
    )]
    private ?TypeService $TypeService = null;

    /**
     * @var Collection<int, DemandeService>
     */
    #[ORM\OneToMany(targetEntity: DemandeService::class, mappedBy: 'service', orphanRemoval: true)]
    private Collection $demandes;

    #[ORM\Column(nullable: true)]
    private ?float $prix = null;

    public function __construct()
    {
        $this->demandes = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, DemandeService>
     */
    public function getDemandes(): Collection
    {
        return $this->demandes;
    }

    public function addDemande(DemandeService $demande): static
    {
        if (!$this->demandes->contains($demande)) {
            $this->demandes->add($demande);
            $demande->setService($this);
        }

        return $this;
    }

    public function removeDemande(DemandeService $demande): static
    {
        if ($this->demandes->removeElement($demande)) {
            // set the owning side to null (unless already changed)
            if ($demande->getService() === $this) {
                $demande->setService(null);
            }
        }

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(?float $prix): static
    {
        $this->prix = $prix;

        return $this;
    }
}