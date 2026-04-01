<?php

namespace App\Entity;

use App\Repository\OutingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: OutingRepository::class)]
class Outing
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;


    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Veuillez donner un nom à votre activité.')]
    #[Assert\Length(min: 3, max: 255,
        minMessage: 'Le nom de votre sortie doit contenir entre 3 et 255.',
        maxMessage: 'Le nom de votre sortie doit contenir entre 3 et 255.')]
    private ?string $name = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'Veuillez sélectionner une date.')]
//    #[Assert\Expression(
//        "value > (new DateTime('+60 minutes'))",
//        message: "La sortie doit commencer dans au moins une heure."
//    )]
    private ?\DateTime $startDateTime = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'Veuillez sélectionner une durée.')]
    private ?int $duration = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'Veuillez sélectionner une date.')]
    #[Assert\LessThan(propertyPath: "startDateTime", message: "La date limite d'inscription doit préceder le début de la sortie.")]
    private ?\DateTime $signupDateLimit = null;

    #[ORM\Column(nullable: true)]
    #[Assert\NotBlank(message: 'Veuillez indiquer le nombre de participants maximum.')]
    #[Assert\GreaterThanOrEqual(value: 3, message: "Le nombre de places ne peut pas être inférieur à 3.")]
    private ?int $nbSignupsMax = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(max: 255, maxMessage: 'Votre déscription ne doit pas dépasser 255 caractères.')]
    private ?string $outingInfo = null;

    #[ORM\ManyToOne(inversedBy: 'outing')]
    #[Assert\NotBlank(message: 'Veuillez sélectionner un lieu de sortie.')]
    private ?Location $location = null;

    #[ORM\ManyToOne(inversedBy: 'myOutings')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL') ]
    private ?User $organiser = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'outings')]
    private Collection $participants;

    #[ORM\ManyToOne(inversedBy: 'outings')]
    private ?Status $status = null;

    #[ORM\ManyToOne(inversedBy: 'outings')]
    private ?Campus $campus = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $photo = null;

    public function __construct()
    {
        $this->participants = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getStartDateTime(): ?\DateTime
    {
        return $this->startDateTime;
    }

    public function setStartDateTime(\DateTime $startDateTime): static
    {
        $this->startDateTime = $startDateTime;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): static
    {
        $this->duration = $duration;

        return $this;
    }

    public function getSignupDateLimit(): ?\DateTime
    {
        return $this->signupDateLimit;
    }

    public function setSignupDateLimit(\DateTime $signupDateLimit): static
    {
        $this->signupDateLimit = $signupDateLimit;

        return $this;
    }

    public function getNbSignupsMax(): ?int
    {
        return $this->nbSignupsMax;
    }

    public function setNbSignupsMax(?int $nbSignupsMax): static
    {
        $this->nbSignupsMax = $nbSignupsMax;

        return $this;
    }

    public function getOutingInfo(): ?string
    {
        return $this->outingInfo;
    }

    public function setOutingInfo(?string $outingInfo): static
    {
        $this->outingInfo = $outingInfo;

        return $this;
    }

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function setLocation(?Location $location): static
    {
        $this->location = $location;

        return $this;
    }

    public function getOrganiser(): ?User
    {
        return $this->organiser;
    }

    public function setOrganiser(?User $organiser): static
    {
        $this->organiser = $organiser;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function addParticipant(User $participant): static
    {
        if (!$this->participants->contains($participant)) {
            $this->participants->add($participant);
        }

        return $this;
    }

    public function removeParticipant(User $participant): static
    {
        $this->participants->removeElement($participant);

        return $this;
    }

    public function getStatus(): ?Status
    {
        return $this->status;
    }

    public function setStatus(?Status $status): static
    {
        $this->status = $status;

        return $this;
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

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): static
    {
        $this->photo = $photo;

        return $this;
    }
}
