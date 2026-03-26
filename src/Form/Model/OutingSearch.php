<?php

namespace App\Form\Model;

use App\Entity\Campus;
use App\Entity\User;
use Doctrine\Common\Collections\Collection;
use phpDocumentor\Reflection\Types\Boolean;

class OutingSearch
{
    private ?Campus $campus = null;

    private ?string $name = null;

    private ?\DateTime $startSearchDate = null;

    private ?\DateTime $endSearchDate = null;

    private ?bool $outingOrganiser = null;

    private ?bool $outingParticipant = null;

    private ?bool $outingNotParticipant = null;

    private ?bool $outingPassed = null;

    private ?User $connectedUser = null;

//    private ?array $outingFilters = null;

    public function getCampus(): ?Campus
    {
        return $this->campus;
    }

    public function setCampus(?Campus $campus): void
    {
        $this->campus = $campus;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getStartSearchDate(): ?\DateTime
    {
        return $this->startSearchDate;
    }

    public function setStartSearchDate(?\DateTime $startSearchDate): void
    {
        $this->startSearchDate = $startSearchDate;
    }

    public function getEndSearchDate(): ?\DateTime
    {
        return $this->endSearchDate;
    }

    public function setEndSearchDate(?\DateTime $endSearchDate): void
    {
        $this->endSearchDate = $endSearchDate;
    }

    public function getOutingOrganiser(): ?bool
    {
        return $this->outingOrganiser;
    }

    public function setOutingOrganiser(?bool $outingOrganiser): void
    {
        $this->outingOrganiser = $outingOrganiser;
    }

    public function getOutingParticipant(): ?bool
    {
        return $this->outingParticipant;
    }

    public function setOutingParticipant(?bool $outingParticipant): void
    {
        $this->outingParticipant = $outingParticipant;
    }

    public function getOutingNotParticipant(): ?bool
    {
        return $this->outingNotParticipant;
    }

    public function setOutingNotParticipant(?bool $outingNotParticipant): void
    {
        $this->outingNotParticipant = $outingNotParticipant;
    }

    public function getOutingPassed(): ?bool
    {
        return $this->outingPassed;
    }

    public function setOutingPassed(?bool $outingPassed): void
    {
        $this->outingPassed = $outingPassed;
    }

    public function getConnectedUser(): ?User
    {
        return $this->connectedUser;
    }

    public function setConnectedUser(?User $connectedUser): void
    {
        $this->connectedUser = $connectedUser;
    }

//    public function getOutingFilters(): ?array
//    {
//        return $this->outingFilters;
//    }
//
//    public function setOutingFilters(?array $outingFilters): void
//    {
//        $this->outingFilters = $outingFilters;
//    }
}
