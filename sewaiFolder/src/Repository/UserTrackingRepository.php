<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\UserTracking;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserTracking>
 *
 * @method UserTracking|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserTracking|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserTracking[]    findAll()
 * @method UserTracking[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserTrackingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserTracking::class);
    }

    public function findByUser(User $user)
    {
        return $this->createQueryBuilder('ut')
        ->join('ut.users', 'u')
        ->where('u.id = :user')
        ->setParameter('user', $user->getId())
        ->getQuery()
        ->getResult();
    }

//    /**
//     * @return UserTracking[] Returns an array of UserTracking objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?UserTracking
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
