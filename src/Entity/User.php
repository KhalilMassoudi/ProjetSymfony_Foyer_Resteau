<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'Cet email est déjà utilisé.')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Assert\NotBlank] // Assurer que le nom d'utilisateur n'est pas vide
    #[Assert\Length(min: 3, max: 180)] // Limiter la longueur entre 3 et 180 caractères
    private ?string $username = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Assert\NotBlank] // Assurer que le mot de passe n'est pas vide
    #[Assert\Length(min: 5)] // Minimum de 5 caractères
    #[Assert\Regex(
        pattern: "/[a-z]/",
        match: true,
        message: "Le mot de passe doit contenir au moins une lettre minuscule."
    )]
    #[Assert\Regex(
        pattern: "/[A-Z]/",
        match: true,
        message: "Le mot de passe doit contenir au moins une lettre majuscule."
    )]
    private ?string $password = null;

    #[ORM\Column(length: 255, nullable: true)]

    private ?string $address  ;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\NotBlank] // Assurer que l'email n'est pas vide
    #[Assert\Email(message: "L'email '{{ value }}' n'est pas valide.")]
    private ?string $email = null;

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['remove'])]
    private ?Reservation $reservation = null;
        /**
     * @ORM\OneToMany(targetEntity=DemandeService::class, mappedBy="user")
     */
    private  $demandeServices;    
    /**
     * @ORM\OneToMany(targetEntity=DemandePlat::class, mappedBy="user")
     */
    private $demandePlats;

    public function __construct()
    {
        $this->demandePlats = new ArrayCollection(); // Initialisation de la collection
        $this->demandeServices = new ArrayCollection(); // Initialisation de la collection
    }

    /**
     * @return Collection|DemandePlat[] 
     */
    public function getDemandePlats(): Collection
    {
        return $this->demandePlats ?? new ArrayCollection();
    }

    public function addDemandePlat(DemandePlat $demandePlat): self
    {
        if (!$this->demandePlats->contains($demandePlat)) {
            $this->demandePlats[] = $demandePlat;
            $demandePlat->setUser($this);
        }

        return $this;
    }

    public function removeDemandePlat(DemandePlat $demandePlat): self
    {
        if ($this->demandePlats->removeElement($demandePlat)) {
            // Set the owning side to null (unless already changed)
            if ($demandePlat->getUser() === $this) {
                $demandePlat->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|DemandeService[]
     */
    public function getDemandeServices(): Collection
    {
        return $this->demandeServices ?? new ArrayCollection();
    }

    public function addDemande(DemandeService $demande): self
    {
        if (!$this->demandeServices->contains($demande)) {
            $this->demandeServices[] = $demande;
            $demande->setUser($this);
        }

        return $this;
    }

    public function removeDemande(DemandeService $demande): self
    {
        if ($this->demandeServices->removeElement($demande)) {
            if ($demande->getUser() === $this) {
                $demande->setUser(null);
            }
        }

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username; // Retourner l'email au lieu du username
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email; // Utiliser l'email comme identifiant
    }


    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @see UserInterface
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // Garantir que chaque utilisateur ait au moins le rôle 'ROLE_USER'
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // Si vous stockez des données sensibles, vous pouvez les effacer ici
        // Par exemple, si vous avez une variable de mot de passe en clair, vous pouvez la définir sur null
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getReservation(): ?Reservation
    {
        return $this->reservation;
    }

    public function setReservation(?Reservation $reservation): self
    {
        $this->reservation = $reservation;

        return $this;
    }


    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $verificationToken = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isVerified = false;

    public function getVerificationToken(): ?string
    {
        return $this->verificationToken;
    }

    public function setVerificationToken(?string $verificationToken): self
    {
        $this->verificationToken = $verificationToken;

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    #[ORM\Column(type: 'integer', options: ["default" => 0])]
    private int $loginCount = 0;

    public function getLoginCount(): int
    {
        return $this->loginCount;
    }

    public function incrementLoginCount(): self
    {
        $this->loginCount++;
        return $this;
    }

}
