<?php

namespace App\Repository;

use App\Entity\WatchStock;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method WatchStock|null find($id, $lockMode = null, $lockVersion = null)
 * @method WatchStock|null findOneBy(array $criteria, array $orderBy = null)
 * @method WatchStock[]    findAll()
 * @method WatchStock[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WatchStockRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WatchStock::class);
    }

    // /**
    //  * @return WatchStock[] Returns an array of WatchStock objects
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
    public function findOneBySomeField($value): ?WatchStock
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
