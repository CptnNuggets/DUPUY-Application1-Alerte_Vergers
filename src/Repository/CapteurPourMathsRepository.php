<?php

namespace App\Repository;

use App\Entity\CapteurPourMaths;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CapteurPourMaths|null find($id, $lockMode = null, $lockVersion = null)
 * @method CapteurPourMaths|null findOneBy(array $criteria, array $orderBy = null)
 * @method CapteurPourMaths[]    findAll()
 * @method CapteurPourMaths[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CapteurPourMathsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CapteurPourMaths::class);
    }

    // /**
    //  * @return CapteurPourMaths[] Returns an array of CapteurPourMaths objects
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
    public function findOneBySomeField($value): ?CapteurPourMaths
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
