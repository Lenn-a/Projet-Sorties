<?php

namespace App\Services;

use App\Entity\Outing;
use App\Repository\StatusRepository;

class StatusService
{
    public StatusRepository $repository;

    public function __construct(StatusRepository $repository) {
        $this->repository = $repository;
    }
    public function statusOpenClose(Outing $outing): void
    {
        if (count($outing->getParticipants()) == $outing->getNbSignupsMax()) {
            $status = $this->repository->getStatusByName('Clôturée');
            $outing->setStatus($status);
        } else {
            $status = $this->repository->getStatusByName('Ouverte');
            $outing->setStatus($status);
        }
    }

    public function setStatusWithName(Outing $outing, string $label): void
    {
        $status = $this->repository->getStatusByName($label);
        $outing->setStatus($status);
    }
}
