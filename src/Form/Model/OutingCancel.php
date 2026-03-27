<?php

namespace App\Form\Model;

class OutingCancel
{
    private ?string $cancelMotive = null;

    public function getCancelMotive(): ?string
    {
        return $this->cancelMotive;
    }

    public function setCancelMotive(?string $cancelMotive): void
    {
        $this->cancelMotive = $cancelMotive;
    }
}
