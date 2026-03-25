<?php

namespace App\Form\Model;

use App\Entity\Outing;
use App\Entity\User;
use Doctrine\Common\Collections\Collection;

class OutingSearch
{
    private ?string $campus = null;

    private ?string $name = null;

    private ?\DateTime $startSearchDate = null;

    private ?\DateTime $endSearchDate = null;

    private ?Collection $outingsOrganiser = null;

    private ?Collection $outingsParticipant = null;

    private ?Collection $outingsNotParticipant = null;

    private ?Collection $outingsPassed = null;

    public function getCampus(): ?string
    {
        return $this->campus;
    }

    public function setCampus(?string $campus): void
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

    public function getOutingsOrganiser(): ?Collection
    {
        return $this->outingsOrganiser;
    }

    public function setOutingsOrganiser(?Collection $outingsOrganiser): void
    {
        $this->outingsOrganiser = $outingsOrganiser;
    }

    public function getOutingsParticipant(): ?Collection
    {
        return $this->outingsParticipant;
    }

    public function setOutingsParticipant(?Collection $outingsParticipant): void
    {
        $this->outingsParticipant = $outingsParticipant;
    }

    public function getOutingsNotParticipant(): ?Collection
    {
        return $this->outingsNotParticipant;
    }

    public function setOutingsNotParticipant(?Collection $outingsNotParticipant): void
    {
        $this->outingsNotParticipant = $outingsNotParticipant;
    }

    public function getOutingsPassed(): ?Collection
    {
        return $this->outingsPassed;
    }

    public function setOutingsPassed(?Collection $outingsPassed): void
    {
        $this->outingsPassed = $outingsPassed;
    }



}
