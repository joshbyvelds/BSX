<?php

namespace App\Repository;

use App\Entity\WizardPlay;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method WizardPlay|null find($id, $lockMode = null, $lockVersion = null)
 * @method WizardPlay|null findOneBy(array $criteria, array $orderBy = null)
 * @method WizardPlay[]    findAll()
 * @method WizardPlay[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WizardPlayRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WizardPlay::class);
    }

    // /**
    //  * @return WizardPlay[] Returns an array of WizardPlay objects
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
    public function findOneBySomeField($value): ?WizardPlay
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
