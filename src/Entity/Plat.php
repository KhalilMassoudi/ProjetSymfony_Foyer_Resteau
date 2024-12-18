<?php

namespace App\Entity;

use App\Repository\PlatRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PlatRepository::class)]
class Plat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255,nullable: true)]
    #[Assert\NotBlank(message: 'Le nom du plat est obligatoire.')]

    #[Assert\Length(
        max: 255,
        maxMessage: 'Le nom du plat ne peut pas dépasser {{ limit }} caractères.'
    )]

    private ?string $nomPlat = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Assert\Length(
        max: 250,
        maxMessage: 'La description du plat ne peut pas dépasser {{ limit }} caractères.'
    )]
    private ?string $descPlat = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'Le prix du plat est obligatoire.')]
    #[Assert\Positive(message: 'Le prix du plat doit être un nombre positif.')]
    private ?float $prixPlat = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le type de cuisine est obligatoire.')]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Le type de cuisine ne peut pas dépasser {{ limit }} caractères.'
    )]
    private ?string $typeCuisine = null;

    #[ORM\Column]
    private bool $dispoPlat = true;

    #[ORM\ManyToOne(targetEntity: CategoriePlat::class, inversedBy: 'plats')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    #[Assert\NotBlank(message: 'La catégorie du plat est obligatoire.')] // Validation ajoutée ici
    private ?CategoriePlat $categoriePlat = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    // Ajout de la relation OneToMany vers DemandePlat
    #[ORM\OneToMany(mappedBy: 'plat', targetEntity: DemandePlat::class)]
    private $demandePlats;

    public function __construct()
    {
        $this->demandePlats = new \Doctrine\Common\Collections\ArrayCollection(); // Initialisation de la collection
    }

    // Méthodes pour gérer la relation OneToMany
    /**
     * @return \Doctrine\Common\Collections\Collection|DemandePlat[]
     */
    public function getDemandePlats(): \Doctrine\Common\Collections\Collection
    {
        return $this->demandePlats;
    }

    public function addDemandePlat(DemandePlat $demandePlat): self
    {
        if (!$this->demandePlats->contains($demandePlat)) {
            $this->demandePlats[] = $demandePlat;
            $demandePlat->setPlat($this); // Associe le plat à la demande de plat
        }

        return $this;
    }

    public function removeDemandePlat(DemandePlat $demandePlat): self
    {
        if ($this->demandePlats->removeElement($demandePlat)) {
            // Définir la relation inverse à null si nécessaire
            if ($demandePlat->getPlat() === $this) {
                $demandePlat->setPlat(null);
            }
        }

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomPlat(): ?string
    {
        return $this->nomPlat;
    }

    public function setNomPlat(string $nomPlat): self
    {
        $this->nomPlat = $nomPlat;
        return $this;
    }

    public function getDescPlat(): ?string
    {
        return $this->descPlat;
    }

    public function setDescPlat(?string $descPlat): self
    {
        $this->descPlat = $descPlat;
        return $this;
    }

    public function getPrixPlat(): ?float
    {
        return $this->prixPlat;
    }

    public function setPrixPlat(float $prixPlat): self
    {
        $this->prixPlat = $prixPlat;
        return $this;
    }

    public function getTypeCuisine(): ?string
    {
        return $this->typeCuisine;
    }

    public function setTypeCuisine(string $typeCuisine): self
    {
        $this->typeCuisine = $typeCuisine;
        return $this;
    }

    public function getCategoriePlat(): ?CategoriePlat
    {
        return $this->categoriePlat;
    }

    public function setCategoriePlat(CategoriePlat $categoriePlat): self
    {
        $this->categoriePlat = $categoriePlat;
        return $this;
    }

    public function getDispoPlat(): bool
    {
        return $this->dispoPlat;
    }

    public function setDispoPlat(bool $dispoPlat): self
    {
        $this->dispoPlat = $dispoPlat;
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
    #[ORM\Column(type: 'integer', nullable: false)]
#[Assert\NotBlank(message: 'La quantité est obligatoire.')]
#[Assert\PositiveOrZero(message: 'La quantité doit être un nombre positif ou zéro.')]
private ?int $quantite = 0;

public function getQuantite(): ?int
{
    return $this->quantite;
}

public function setQuantite(?int $quantite): self
{
    $this->quantite = $quantite;
    return $this;
}
public function decrementQuantite(): self
{
    if ($this->quantite > 0) {
        $this->quantite--;
    }
    return $this;
}

#[ORM\Column(type: 'datetime', nullable: false)]
#[Assert\NotBlank(message: 'La date de création est obligatoire.')]
private ?\DateTimeInterface $createdAt = null;

public function getCreatedAt(): ?\DateTimeInterface
{
    return $this->createdAt;
}

public function setCreatedAt(\DateTimeInterface $createdAt): self
{
    $this->createdAt = $createdAt;
    return $this;
}
}
