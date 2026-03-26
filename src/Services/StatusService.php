<?php

namespace App\Services;

use App\Entity\Outing;
use App\Repository\StatusRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpParser\Node\Scalar\String_;

class StatusService
{
    private StatusRepository $repository;
    private EntityManagerInterface $entityManager;

    public function __construct(StatusRepository $repository, EntityManagerInterface $entityManager) {
        $this->repository = $repository;
        $this->entityManager = $entityManager;
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

    public function setStatusByDate(Outing $outing): void
    {
        $now = new \DateTime('now');
        $limitHistorisee = new \DateTime('-1 month');
        $outingDate = $outing->getStartDateTime();
        $signupDate = $outing->getSignupDateLimit();
        $currentStatus = $outing->getStatus()->getLabel();
        $endOuting = $outing->getStartDateTime()->modify('+' . $outing->getDuration() . ' minutes');
        $statusLabel = 'Ouverte';
//        if ($outing->getId() == 297) {
//            dump($outingDate < $limitHistorisee);
//            dump($currentStatus == 'Annulée');
//            dump($outingDate > $now && $outingDate < $endOuting);
//            dump($now > $endOuting);
//            dump($currentStatus == 'Cloturée' || $signupDate < $now);

        switch (true) {
            case $currentStatus == 'En création' : $statusLabel = 'En création'; break;
            case $outingDate <= $limitHistorisee: $statusLabel = 'Historisée';break;
            case $currentStatus == 'Annulée' : $statusLabel = 'Annulée'; break;
            case $outingDate > $now && $outingDate < $endOuting: $statusLabel = 'En cours';break;
            case $now > $endOuting : $statusLabel = 'Terminée';break;
            case $currentStatus == 'Cloturée' || $signupDate < $now: $statusLabel= 'Clôturée'; break;
        }


        $status = $this->repository->getStatusByName($statusLabel);
        $outing->setStatus($status);
        $this->entityManager->persist($outing);
        $this->entityManager->flush();
        }
//    }
}
