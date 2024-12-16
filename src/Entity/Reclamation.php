<?php

namespace App\Entity;

use App\Repository\ReclamationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Knp\RatingBundle\Annotation as Rating;

/**
 * @Rating\Rateable
 */

#[ORM\Entity(repositoryClass: ReclamationRepository::class)]
class Reclamation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null; // Nouveau champ titre obligatoire

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $image;
    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_Reclamation = null;

    #[ORM\Column(length: 255)]
    private ?string $etat = 'suspendue'; // Etat par défaut

    #[ORM\Column(length: 255)]
    private ?string $reponse = '';

    #[ORM\ManyToOne]
    private ?TypeReclamation $typeReclamations = null; // Réponse par défaut est une chaîne vide

    #[ORM\Column(type: "integer")]
    private $rating = 0;
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(type: 'boolean')]
    private bool $favori = false;

    public function isFavori(): bool
    {
        return $this->favori;
    }

    public function setFavori(bool $favori): self
    {
        $this->favori = $favori;
        return $this;
    }

    // Getter et Setter pour rating
    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(?int $rating): self
    {
        $this->rating = $rating;

        return $this;
    }
    // Constructeur pour définir les valeurs par défaut (facultatif)
    public function __construct()
    {
        // Définit la date actuelle lors de la création de la réclamation
        $this->date_Reclamation = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    // Getter et Setter pour le champ titre
    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getDateReclamation(): ?\DateTimeInterface
    {
        return $this->date_Reclamation;
    }

    public function setDateReclamation(\DateTimeInterface $date_Reclamation): static
    {
        $this->date_Reclamation = $date_Reclamation;
        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): static
    {
        $this->etat = $etat;
        return $this;
    }

    public function getReponse(): ?string
    {
        return $this->reponse;
    }

    public function setReponse(string $reponse): static
    {
        $this->reponse = $reponse;

        // Si une réponse est définie, l'état devient "répondue"
        if (!empty($reponse)) {
            $this->etat = 'répondue';
        }

        return $this;
    }

    public function getTypeReclamations(): ?TypeReclamation
    {
        return $this->typeReclamations;
    }

    public function setTypeReclamations(?TypeReclamation $typeReclamations): static
    {
        $this->typeReclamations = $typeReclamations;
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
    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

}
