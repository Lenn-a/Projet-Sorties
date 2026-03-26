<?php

namespace App\Repository;

use App\Entity\Outing;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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

    public function findAllPublishedOutings() {
//        SELECT * FROM `outing` LEFT JOIN status on outing.status_id = status.id
//WHERE status.label = 'Ouverte'
        $queryBuilder = $this->createQueryBuilder('o');
        $queryBuilder
            ->leftJoin('o.status', 'status')
            ->addSelect('status')
            ->Where('status.label = :ouverte')->setParameter('ouverte', 'Ouverte')
            ->orWhere('status.label = :terminee')->setParameter('terminee', 'Terminée')
            ->orWhere('status.label = :encours')->setParameter('encours', 'En cours')
            ->orWhere('status.label = :cloturee')->setParameter('cloturee', 'Clôturée')
            ->orWhere('status.label = :annulee')->setParameter('annulee', 'Annulée')
        ;
        return $queryBuilder->getQuery()->getResult();
    }

    public function findMyOutings(){
        $queryBuilder = $this->createQueryBuilder('ec');
        $queryBuilder
            ->leftJoin('ec.status', 'status')
            ->addSelect('status')
            ->Where('status.label = :encreation')->setParameter('encreation', 'En création');
        return $queryBuilder->getQuery()->getResult();
    }
}
