<?php

namespace App\Repository;

use App\Entity\AssocCapteurStation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AssocCapteurStation|null find($id, $lockMode = null, $lockVersion = null)
 * @method AssocCapteurStation|null findOneBy(array $criteria, array $orderBy = null)
 * @method AssocCapteurStation[]    findAll()
 * @method AssocCapteurStation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AssocCapteurStationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AssocCapteurStation::class);
    }

    // /**
    //  * @return AssocCapteurStation[] Returns an array of AssocCapteurStation objects
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
    public function findOneBySomeField($value): ?AssocCapteurStation
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
