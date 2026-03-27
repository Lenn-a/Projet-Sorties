<?php

namespace App\Repository;

use App\Entity\Outing;
use App\Form\Model\OutingSearch;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Outing>
 */
class OutingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Outing::class);
    }

    public function findOutingsPastMonth() {
        $queryBuilder = $this->createQueryBuilder('o');
        $queryBuilder->where('o.startDateTime <= :date')->setParameter('date', new DateTime('-1 month'))
                     ->orderBy('o.startDateTime', 'DESC');
        return $queryBuilder->getQuery()->getResult();
    }

    public function findAllPublishedOutings(OutingSearch $outingSearch) {

//        SELECT * FROM `outing` LEFT JOIN status on outing.status_id = status.id
//WHERE status.label = 'Ouverte'
        $queryBuilder = $this->createQueryBuilder('o');
        // DISPLAY only PUBLISHED outings
        $queryBuilder
            ->leftJoin('o.status', 'status')
            ->addSelect('status')

            ->andWhere('status.label = :ouverte')->setParameter('ouverte', 'Ouverte')
            ->orWhere('status.label = :terminee')->setParameter('terminee', 'Terminée')
            ->orWhere('status.label = :encours')->setParameter('encours', 'En cours')
            ->orWhere('status.label = :cloturee')->setParameter('cloturee', 'Clôturée')
            ->orWhere('status.label = :annulee')->setParameter('annulee', 'Annulée');
        // Filter outings by CAMPUS
        if ($outingSearch->getCampus()) {
            $queryBuilder
                ->leftJoin('o.campus', 'campus')
                ->addSelect('campus')
                ->andWhere('o.campus = :campus')->setParameter('campus', $outingSearch->getCampus());
        }
        // Filter outings by NAME
        if ($outingSearch->getName()) {
            $queryBuilder
                ->andWhere('o.name LIKE :name')->setParameter('name', '%' . $outingSearch->getName() . '%');
        }
        // Filter outings by DATE
        if ($outingSearch->getStartSearchDate()) {
            $queryBuilder
                ->andWhere('o.startDateTime >= :startSearchDate')->setParameter('startSearchDate', $outingSearch->getStartSearchDate());
        }

        if ($outingSearch->getEndSearchDate()) {
            $queryBuilder
                ->andWhere('o.startDateTime <= :endSearchDate')->setParameter('endSearchDate', $outingSearch->getEndSearchDate());
        }
        // Filter outings by ORGANISER
        if ($outingSearch->getConnectedUser() and $outingSearch->getOutingOrganiser()) {
            $queryBuilder
                ->andWhere('o.organiser = :organiser')->setParameter('organiser', $outingSearch->getConnectedUser());
        }
        // Filter outings by PARTICIPANT
        if ($outingSearch->getConnectedUser() and $outingSearch->getOutingParticipant()) {
            $queryBuilder
                ->andWhere(':participant MEMBER OF o.participants')->setParameter('participant', $outingSearch->getConnectedUser());
        }
        // Filter outings by NOT PARTICIPANT
        if ($outingSearch->getConnectedUser() and $outingSearch->getOutingNotParticipant()) {
            $queryBuilder
                ->andWhere(':participant NOT MEMBER OF o.participants')->setParameter('participant', $outingSearch->getConnectedUser());
        }

        // Filter outings by PASSED DATE
        if ($outingSearch->getOutingPassed()) {
            $queryBuilder
                ->andWhere('DATE_ADD(o.startDateTime, o.duration, \'MINUTE\') < :passed')->setParameter('passed', $outingSearch->getCurrentDateTime());
        }

        $queryBuilder->orderBy('o.startDateTime', 'ASC');

        return $queryBuilder->getQuery()->getResult();
    }

    public function findMyOutings(){
        $queryBuilder = $this->createQueryBuilder('ec');
        $queryBuilder
            ->Join('ec.status', 'status')
            ->addSelect('status')
            ->Where('status.label = :encreation')->setParameter('encreation', 'En création');
        return $queryBuilder->getQuery()->getResult();
    }
}
