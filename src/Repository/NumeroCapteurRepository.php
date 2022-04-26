<?php

namespace App\Repository;

use App\Entity\NumeroCapteur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method NumeroCapteur|null find($id, $lockMode = null, $lockVersion = null)
 * @method NumeroCapteur|null findOneBy(array $criteria, array $orderBy = null)
 * @method NumeroCapteur[]    findAll()
 * @method NumeroCapteur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NumeroCapteurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NumeroCapteur::class);
    }

    // /**
    //  * @return NumeroCapteur[] Returns an array of NumeroCapteur objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('n.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?NumeroCapteur
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
