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

            #[ORM\ManyToOne(targetEntity: Chambre::class)]
            #[ORM\JoinColumn(nullable: false)]
            private ?Chambre $chambre = null;

            #[ORM\Column(type: 'datetime')]
            private $dateReservation;

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
            #[ORM\OneToOne(inversedBy: 'reservation')]
            #[ORM\JoinColumn(nullable: false)] // Pas de cascade ici
            private ?User $user = null;
            #[ORM\Column(type: 'string', length: 50, options: ['default' => 'En attente'])]
            private ?string $statut = 'En attente';

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
            public function getUser(): ?User
            {
                return $this->user;
            }

            public function setUser(?User $user): self
            {
                $this->user = $user;

                return $this;
            }
            public function getDateReservation(): ?\DateTimeInterface
            {
                return $this->dateReservation;
            }

            public function setDateReservation(\DateTimeInterface $dateReservation): self
            {
                $this->dateReservation = $dateReservation;

                return $this;
            }
            public function getStatut(): string
            {
                return $this->statut;
            }
            public function setStatut(string $statut): self
            {
                $this->statut = $statut;
                return $this;
            }
            public function isOverlap(Reservation $otherReservation): bool
            {
                $start = $this->getDateArrivee();
                $end = $this->getDateDepart();
                $otherStart = $otherReservation->getDateArrivee();
                $otherEnd = $otherReservation->getDateDepart();

                // Vérifie si les plages de dates se chevauchent
                return ($start < $otherEnd) && ($end > $otherStart);
            }
        }
