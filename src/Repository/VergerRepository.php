<?php

namespace App\Repository;

use App\Entity\Verger;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Verger|null find($id, $lockMode = null, $lockVersion = null)
 * @method Verger|null findOneBy(array $criteria, array $orderBy = null)
 * @method Verger[]    findAll()
 * @method Verger[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VergerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Verger::class);
    }

    // /**
    //  * @return Verger[] Returns an array of Verger objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('v.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Verger
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
