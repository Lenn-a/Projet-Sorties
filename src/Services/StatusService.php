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

    public function setStatusByDate(): void
    {
        $outings = $this->outingRepository->outingsThatCanChange();

        $now = new \DateTime('now');
        $limitHistorisee = $now->sub(new \DateInterval('P1M'));
        $ouverte = $this->repository->getStatusByName('Ouverte');
        $historisee = $this->repository->getStatusByName('Historisée');
        $annulee = $this->repository->getStatusByName('Annulée');
        $encours = $this->repository->getStatusByName('En cours');
        $terminee = $this->repository->getStatusByName('Terminée');
        $cloturee = $this->repository->getStatusByName('Clôturée');


        foreach ($outings as $outing) {
            $outingStart = $outing->getStartDateTime();

            $signupDate = $outing->getSignupDateLimit();
            $currentStatus = $outing->getStatus()->getLabel();
            $maxParticipants = $outing->getNbSignupsMax();
            $inscrits = $outing->getParticipants()->count();

            if ($outing->getId() == 111) {
                dump($outing);
            }

            $endOuting = date_add( $outing->getStartDateTime(), date_interval_create_from_date_string( $outing->getDuration() . 'minutes'));
            $finalStatus = $ouverte;
            switch (true) {
                case $outingStart < $limitHistorisee:
                    $finalStatus = $historisee;
                    break;
                case $currentStatus === 'Annulée' :
                    $finalStatus = $annulee;
                    break;
                case $now > $outingStart && $now < $endOuting:
                    $finalStatus = $encours;
                    break;
                case $now > $endOuting :
                    $finalStatus = $terminee;
                    break;
                case $inscrits >= $maxParticipants || $now >= $signupDate:
                    $finalStatus = $cloturee;
                    break;
            }

            $outing->setStatus($finalStatus);
            $this->entityManager->persist($outing);
        }

        $this->entityManager->flush();
    }
}
