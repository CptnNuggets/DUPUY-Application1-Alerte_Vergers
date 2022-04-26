<?php

namespace App\Repository;

use App\Entity\ValeursCumulees;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ValeursCumulees|null find($id, $lockMode = null, $lockVersion = null)
 * @method ValeursCumulees|null findOneBy(array $criteria, array $orderBy = null)
 * @method ValeursCumulees[]    findAll()
 * @method ValeursCumulees[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ValeursCumuleesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ValeursCumulees::class);
    }

    // /**
    //  * @return ValeursCumulees[] Returns an array of ValeursCumulees objects
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
    public function findOneBySomeField($value): ?ValeursCumulees
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
