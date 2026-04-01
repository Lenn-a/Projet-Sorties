<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'Pas disponible, veuillez renseigner une autre adresse mail.')]
#[UniqueEntity(fields: ['username'], message: 'Pas disponible, veuillez renseigner un autre pseudo.')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(name: 'email', length: 180, unique: true)]
    #[Assert\NotBlank(message: 'Ce champs est obligatoire.')]
    #[Assert\Email(message: 'Veuillez renseigner une adresse email valide.')]
    #[Assert\Length(min: 6, max: 180,
        minMessage: 'Veuillez renseigner une adresse mail entre 3 à 180 caractères.',
        maxMessage: 'Veuillez renseigner une adresse mail entre 3 à 180 caractères.'
    )]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: 'Ce champs est obligatoire.')]
    #[Assert\Length(min: 2, max: 50)]
    private ?string $firstname = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: 'Ce champs est obligatoire.')]
    #[Assert\Length(min: 2, max: 50)]
    private ?string $lastname = null;

    #[ORM\Column(name: 'username', length: 50, unique: true)]
    #[Assert\NotBlank(message: 'Ce champs est obligatoire.')]
    #[Assert\Length(min: 5, max: 50)]
    private ?string $username = null;

    #[ORM\Column(length: 15)]
    private ?string $phone = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $photo = null;

    /**
     * @var Collection<int, Outing>
     */
    #[ORM\ManyToMany(targetEntity: Outing::class, mappedBy: 'participants')]
    private Collection $outings;

    #[ORM\OneToMany(targetEntity: Outing::class, mappedBy: 'organiser')]
    private Collection $myOutings;

    #[ORM\Column]
    private ?bool $active = null;

    #[ORM\ManyToOne(targetEntity: Campus::class, inversedBy: 'users')]
    private ?Campus $campus = null;

    public function __construct()
    {
        $this->outings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
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

    public function setPassword(?string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Ensure the session doesn't contain actual password hashes by CRC32C-hashing them, as supported since Symfony 7.3.
     */
    public function __serialize(): array
    {
        $data = (array) $this;
        $data["\0".self::class."\0password"] = hash('crc32c', $this->password);

        return $data;
    }

    #[\Deprecated]
    public function eraseCredentials(): void
    {
        // @deprecated, to be removed when upgrading to Symfony 8
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): static
    {
        $this->photo = $photo;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): static
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return Collection<int, Outing>
     */
    public function getOutings(): Collection
    {
        return $this->outings;
    }

    public function addOuting(Outing $outing): static
    {
        if (!$this->outings->contains($outing)) {
            $this->outings->add($outing);
            $outing->setOrganiser($this);
        }

        return $this;
    }

    public function removeOuting(Outing $outing): static
    {
        if ($this->outings->removeElement($outing)) {
            // set the owning side to null (unless already changed)
            if ($outing->getOrganiser() === $this) {
                $outing->setOrganiser(null);
            }
        }

        return $this;
    }

    public function getMyOutings(): Collection
    {
        return $this->myOutings;
    }

    public function setMyOutings(Collection $myOutings): void
    {
        $this->myOutings = $myOutings;
    }

    public function getCampus(): ?Campus
    {
        return $this->campus;
    }

    public function setCampus(?Campus $campus): static
    {
        $this->campus = $campus;

        return $this;
    }

    #[ORM\PreUpdate]
    public function preUpdate()
    {
        $this->password = $this->getPassword();
    }

    // A USER who is INACTIVE cannot LOG IN
    public function isEnabled(): bool
    {
        return $this->active;
    }
}
