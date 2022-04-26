<?php

namespace App\Repository;

use App\Entity\Constructeur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Constructeur|null find($id, $lockMode = null, $lockVersion = null)
 * @method Constructeur|null findOneBy(array $criteria, array $orderBy = null)
 * @method Constructeur[]    findAll()
 * @method Constructeur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConstructeurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Constructeur::class);
    }

    // /**
    //  * @return Constructeur[] Returns an array of Constructeur objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Constructeur
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
