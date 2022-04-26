<?php

namespace App\Repository;

use App\Entity\AssocCapteurStation;
use App\Entity\Mesure;
use App\Entity\NumeroCapteur;
use App\Entity\Station;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Proxies\__CG__\App\Entity\Station as EntityStation;

/**
 * @method Mesure|null find($id, $lockMode = null, $lockVersion = null)
 * @method Mesure|null findOneBy(array $criteria, array $orderBy = null)
 * @method Mesure[]    findAll()
 * @method Mesure[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MesureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mesure::class);
    }

    // Selects the last $hours mesures associated to the $station
    // and the $numeroCapteur
    public function findByStationAndCapteur(AssocCapteurStation $aCS, int $hours) : array
    {
        // $stationId = $station->getId()->toBinary();
        // $numCapteurId = $numeroCapteur->getId()->toBinary();
        $aCSID = $aCS->getId()->toBinary();
        return $this->createQueryBuilder('m')
            ->innerJoin('m.assocCapteurStation','a')
            // ->innerJoin('m.station','s')
            // ->innerJoin('m.numeroCapteur','nc')
            ->andWhere('a.id = :aCSID')
            // ->andWhere('s.id = :stationId')
            // ->andWhere('nc.id = :numCapteurId')
            ->setParameter('aCSID', $aCSID)
            // ->setParameter('stationId', $stationId)
            // ->setParameter('numCapteurId', $numCapteurId)
            ->orderBy('m.dateTime', 'DESC')
            ->setMaxResults($hours)
            ->getQuery()
            ->getResult()
        ;
    }


    // // Selects the last $hours mesures associated to the $station
    // // and the $numeroCapteur
    // public function findByStationAndCapteur(Station $station, 
    //     NumeroCapteur $numeroCapteur, int $hours) : array
    // {
    //     $stationId = $station->getId()->toBinary();
    //     $numCapteurId = $numeroCapteur->getId()->toBinary();
    //     return $this->createQueryBuilder('m')
    //         ->innerJoin('m.station','s')
    //         ->innerJoin('m.numeroCapteur','nc')
    //         ->andWhere('s.id = :stationId')
    //         ->andWhere('nc.id = :numCapteurId')
    //         ->setParameter('stationId', $stationId)
    //         ->setParameter('numCapteurId', $numCapteurId)
    //         ->orderBy('m.dateTime', 'DESC')
    //         ->setMaxResults($hours)
    //         ->getQuery()
    //         ->getResult()
    //     ;
    // }

    

    /*
    public function findOneBySomeField($value): ?Mesure
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
