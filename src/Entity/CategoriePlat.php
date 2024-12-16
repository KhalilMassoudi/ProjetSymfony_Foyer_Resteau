<?php

namespace App\Entity;

use App\Repository\CategoriePlatRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CategoriePlatRepository::class)]
class CategoriePlat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: 'Le nom de la catégorie est obligatoire.')]
    #[Assert\Length(
        max: 100,
        maxMessage: 'Le nom de la catégorie ne peut pas dépasser {{ limit }} caractères.'
    )]
    private ?string $nomCategorie = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(
        max: 255,
        maxMessage: 'La description ne peut pas dépasser {{ limit }} caractères.'
    )]
    #[Assert\Regex(
        pattern: "/^[a-zA-Z0-9\s]+$/", 
        message: "La description ne peut contenir que des lettres, des chiffres et des espaces."
    )]
    private ?string $descrCategorie = null;

    /**
     * @var Collection<int, Plat>
     */
    #[ORM\OneToMany(mappedBy: 'categoriePlat', targetEntity: Plat::class, cascade: ['remove'], orphanRemoval: false)]
    private Collection $plats;

    public function __construct()
    {
        $this->plats = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomCategorie(): ?string
    {
        return $this->nomCategorie;
    }

    public function setNomCategorie(string $nomCategorie): static
    {
        $this->nomCategorie = $nomCategorie;

        return $this;
    }

    public function getDescrCategorie(): ?string
    {
        return $this->descrCategorie;
    }

    public function setDescrCategorie(?string $descrCategorie): static
    {
        $this->descrCategorie = $descrCategorie;

        return $this;
    }

    /**
     * @return Collection<int, Plat>
     */
    public function getPlats(): Collection
    {
        return $this->plats;
    }

    public function addPlat(Plat $plat): static
    {
        if (!$this->plats->contains($plat)) {
            $this->plats->add($plat);
            $plat->setCategoriePlat($this);
        }

        return $this;
    }

    public function removePlat(Plat $plat): static
    {
        if ($this->plats->removeElement($plat)) {
            // set the owning side to null (unless already changed)
            if ($plat->getCategoriePlat() === $this) {
                $plat->setCategoriePlat(null);
            }
        }

        return $this;
    }

    /**
     * Méthode __toString() pour afficher le nom de la catégorie dans les formulaires Symfony.
     */
    public function __toString(): string
    {
        return $this->nomCategorie ?? 'Catégorie';
    }
}
