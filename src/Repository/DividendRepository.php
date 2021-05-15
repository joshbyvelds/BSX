<?php

namespace App\Repository;

use App\Entity\Dividend;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Dividend|null find($id, $lockMode = null, $lockVersion = null)
 * @method Dividend|null findOneBy(array $criteria, array $orderBy = null)
 * @method Dividend[]    findAll()
 * @method Dividend[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DividendRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Dividend::class);
    }

    // /**
    //  * @return Dividend[] Returns an array of Dividend objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Dividend
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
