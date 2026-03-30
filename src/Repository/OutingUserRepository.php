<?php

namespace App\Repository;

use App\Entity\OutingUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OutingUser>
 */
class OutingUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OutingUser::class);
    }


    /**
     * @param int $outingId id de la sortie recherchée
     * @return array liste les paires userId/outinId correspondantes
     */
    public function findOutingUsersByOutingId(int $outingId): array{
        $result = $this->createQueryBuilder('io')
            ->where('io.outingId = :outingId')
            ->setParameter('outingId', $outingId)
            ->getQuery()
            ->getResult();
        return $result;
    }
}
