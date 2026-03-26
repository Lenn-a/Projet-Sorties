<?php

namespace App\Form\Model;

use App\Entity\Campus;
use Doctrine\Common\Collections\Collection;
use phpDocumentor\Reflection\Types\Boolean;

class OutingSearch
{
    private ?Campus $campus = null;

    private ?string $name = null;

    private ?\DateTime $startSearchDate = null;

    private ?\DateTime $endSearchDate = null;

    private ?array $outingFilters = null;

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

    public function getOutingFilters(): ?array
    {
        return $this->outingFilters;
    }

    public function setOutingFilters(?array $outingFilters): void
    {
        $this->outingFilters = $outingFilters;
    }
}
