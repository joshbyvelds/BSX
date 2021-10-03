<?php

namespace App\Repository;

use App\Entity\WatchOption;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method WatchOption|null find($id, $lockMode = null, $lockVersion = null)
 * @method WatchOption|null findOneBy(array $criteria, array $orderBy = null)
 * @method WatchOption[]    findAll()
 * @method WatchOption[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WatchOptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WatchOption::class);
    }

    // /**
    //  * @return WatchOption[] Returns an array of WatchOption objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('w.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?WatchOption
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
