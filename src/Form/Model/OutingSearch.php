<?php

namespace App\Form\Model;

use phpDocumentor\Reflection\Types\Boolean;

class OutingSearch
{
    private ?string $campus = null;

    private ?string $name = null;

    private ?\DateTime $startSearchDate = null;

    private ?\DateTime $endSearchDate = null;

    private ?boolean $outingOrganiser = null;

    private ?boolean $outingParticipant = null;

    private ?boolean $outingNotParticipant = null;

    private ?boolean $outingPassed = null;

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
}
