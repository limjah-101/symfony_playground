<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findUserWithMoreThan5PostsQuery(): QueryBuilder
    {
        return $this->createQueryBuilder('u')
            ->select('u')
            ->innerJoin('u.posts', 'p')
            ->groupBy('u')
            ->having('count(p) > 5');
    }

    public function getUsersWith5PostsMoreExceptAuthenticatedUser(User $user)
    {
        return $this->findUserWithMoreThan5PostsQuery()
            ->andHaving('u != :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

}
