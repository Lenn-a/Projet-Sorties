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

    public function findAllPublishedOutings(OutingSearch $outingSearch)
    {

//        SELECT * FROM `outing` LEFT JOIN status on outing.status_id = status.id
//WHERE status.label = 'Ouverte'
        $queryBuilder = $this->createQueryBuilder('o');
//        $queryBuilder->select('o.name', 'o.signupDateLimit', 'o.nbSignupsMax', 'o.startDateTime', 'o.photo')
        $queryBuilder->innerJoin('o.campus', 'c')
            ->addSelect('c')
            ->innerJoin('o.organiser', 'u')
            ->addSelect('u')
            ->leftJoin('o.participants', 'p')
            ->addSelect('p')
            ->innerJoin('o.status', 's')
            ->addSelect('s')
            ->leftJoin('o.location', 'l')
            ->addSelect('l')
            ->leftJoin('l.city', 'v')
            ->addSelect('v')
        ;
        // DISPLAY only PUBLISHED outings
        $queryBuilder
            ->leftJoin('o.status', 'status')
            ->addSelect('status')

//        $queryBuilder
//            ->leftJoin('o.status', 'status')
//            ->addSelect('status');

            ->andWhere('s.label = :ouverte')->setParameter('ouverte', 'Ouverte')
            ->orWhere('s.label = :terminee')->setParameter('terminee', 'Terminée')
            ->orWhere('s.label = :encours')->setParameter('encours', 'En cours')
            ->orWhere('s.label = :cloturee')->setParameter('cloturee', 'Clôturée')
            ->orWhere('s.label = :annulee')->setParameter('annulee', 'Annulée')
            ->orWhere('s.label = :encreation')->setParameter('encreation', 'En creation');
        // Filter outings by CAMPUS
        if ($outingSearch->getCampus()) {
            $queryBuilder
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


    public function findMyOutings(int $organiserId){
        $queryBuilder = $this->createQueryBuilder('ec');
        $queryBuilder->innerJoin('ec.status', 'status')
            ->addSelect('status')
            ->where('status.label = :encreation')->setParameter('encreation', 'En creation')
            ->andWhere('ec.organiser = :organiser')->setParameter('organiser', $organiserId);
        return $queryBuilder->getQuery()->getResult();
    }

    public function outingsThatCanChange()
    {
        $queryBuilder = $this->createQueryBuilder('o');
        $queryBuilder->innerJoin('o.status', 's')
            ->addSelect('s')
            ->where('s.label = :ouverte')->setParameter('ouverte', 'Ouverte')
            ->orWhere('s.label = :cloturee')->setParameter('cloturee', 'Clôturée')
            ->orWhere('s.label = :encours')->setParameter('encours', 'En cours')
            ->orWhere('s.label = :terminee')->setParameter('terminee', 'Terminée')
            ->orWhere('s.label = :annulee')->setParameter('annulee', 'Annulée')
            ->innerJoin('o.participants', 'p')
            ->addSelect('p')
            ;
        return $queryBuilder->getQuery()->getResult();
    }

}
