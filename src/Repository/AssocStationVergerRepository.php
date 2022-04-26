<?php

namespace App\Repository;

use App\Entity\AssocStationVerger;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AssocStationVerger|null find($id, $lockMode = null, $lockVersion = null)
 * @method AssocStationVerger|null findOneBy(array $criteria, array $orderBy = null)
 * @method AssocStationVerger[]    findAll()
 * @method AssocStationVerger[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AssocStationVergerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AssocStationVerger::class);
    }

    // /**
    //  * @return AssocStationVerger[] Returns an array of AssocStationVerger objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?AssocStationVerger
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
