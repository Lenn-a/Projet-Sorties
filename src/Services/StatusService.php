<?php

namespace App\Services;

use App\Entity\Outing;
use App\Repository\OutingRepository;
use App\Repository\StatusRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpParser\Node\Scalar\String_;

class StatusService
{
    private StatusRepository $repository;
    private OutingRepository $outingRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(StatusRepository $repository, EntityManagerInterface $entityManager, OutingRepository $outingRepository)
    {
        $this->repository = $repository;
        $this->outingRepository = $outingRepository;
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

    public function setStatusByDate()
    {
        $outings = $this->outingRepository->outingsThatCanChange();

        $now = new \DateTime('now');
        $limitHistorisee = new \DateTime('-1 month');
        $ouverte = $this->repository->getStatusByName('Ouverte');
        $historisee = $this->repository->getStatusByName('Historisée');
        $annulee = $this->repository->getStatusByName('Annulée');
        $encours = $this->repository->getStatusByName('En cours');
        $terminee = $this->repository->getStatusByName('Terminée');
        $cloturee = $this->repository->getStatusByName('Clôturée');
        $finalStatus = $ouverte;

        foreach ($outings as $outing) {
            $outingDate = $outing->getStartDateTime();
            $signupDate = $outing->getSignupDateLimit();
            $currentStatus = $outing->getStatus()->getLabel();
            $endOuting = $outing->getStartDateTime()->modify('+' . $outing->getDuration() . ' minutes');

            switch (true) {
                case $outingDate <= $limitHistorisee:
                    $finalStatus = $historisee;
                    break;
                case $currentStatus == 'Annulée' :
                    $finalStatus = $annulee;
                    break;
                case $outingDate > $now && $outingDate < $endOuting:
                    $finalStatus = $encours;
                    break;
                case $now > $endOuting :
                    $finalStatus = $terminee;
                    break;
                case $currentStatus == 'Clôturée' || $signupDate < $now:
                    $finalStatus = $cloturee;
                    break;
            }
            $outing->setStatus($finalStatus);
            $this->entityManager->persist($outing);
        }

        $this->entityManager->flush();
    }
}
