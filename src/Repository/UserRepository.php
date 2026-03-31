<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface, UserLoaderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function loadUserByIdentifier(string $identifier): ?UserInterface
    {
        $qb = $this->createQueryBuilder('u');
        $qb
            ->where('u.email = :identifier')
            ->orWhere('u.username = :identifier')
            ->setParameter('identifier', $identifier);

        $user = $qb->getQuery()->getOneOrNullResult();
        return $user;
    }

    /**
     * @param int[] $userIds tableau des id de users recherchés
     * @return User[] liste des users correspondant
     */
    public function findUsersById(array $userIds): array {
        return $this->createQueryBuilder('u')
            ->andWhere('u.id IN (:ids)')
            ->setParameter('ids', $userIds)
            ->getQuery()
            ->getResult();
    }
}
