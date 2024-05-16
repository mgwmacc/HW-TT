<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

        /**
         * @return User[] Returns an array of User objects by group id
         */
        public function findAllByGroupId($value): array
        {
            return $this->createQueryBuilder('u')
                ->andWhere('u.group = :val')
                ->setParameter('val', $value)
                ->orderBy('u.id', 'ASC')
                ->getQuery()
                ->getResult()
            ;
        }

    //    public function findOneBySomeField($value): ?User
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
