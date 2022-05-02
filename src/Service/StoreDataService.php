<?php

namespace App\Service;

use App\Entity\Mesure;
use App\Repository\AssocCapteurStationRepository;
use App\Repository\StationRepository;
use App\Repository\MesureRepository;
use Doctrine\ORM\EntityManagerInterface;

    // General service containing the necessary functions to STORE MESURES in the DATABASE

class StoreDataService
{
        // Entity Manager
    private $em;
        // Repository of the stations
    private $stationRepo;
        // Repository for the mesures
    private $mesureRepo;
        // Repository for the association between capteur and station
    private $aCSRepo;

    public function __construct(EntityManagerInterface $em, StationRepository $stationRepo, 
            MesureRepository $mesureRepo, AssocCapteurStationRepository $aCSRepo)
    {
        $this->em = $em;
        $this->stationRepo = $stationRepo;
        $this->mesureRepo = $mesureRepo;
        $this->aCSRepo = $aCSRepo;
    }



    // Function to STORE A MESURE inside the database, arguments needed :

        // The mesure value (float)
        // the station API code (string)
        // the sensor API code (string)
        // the date-time of the mesure, rounded to the hour (datetime Y-m-d H:i:s)

    public function persistMesure ($valeur, $stationCode, $codeCapteur, $dateTime)
    {
        
        $station = $this->stationRepo->findOneBy(['stationCode' => $stationCode]);
        $aCS = $this->aCSRepo->findOneBy(['station' => $station, 'codeCapteur' => $codeCapteur]);

            // VERIFICATION that the MESURE is NOT ALLREADY in the db
        $existsAllready = $this->mesureRepo->findOneBy([
            'assocCapteurStation' => $aCS,
            'dateTime' => $dateTime
        ]);

            // STORES the mesure if it DOESNT EXIST ALLREADY
        if ($existsAllready == null){
            $mesure = new Mesure;
            $mesure->setValeur($valeur);
            $mesure->setAssocCapteurStation($aCS);
            $mesure->setDateTime($dateTime);

            $this->em->persist($mesure);
            $this->em->flush();
        }
    }




        // Function to verify the LAST DATETIME for mesures related to a station existing in the database

    public function getLastDateTimeInDB ($stationCode)
    {
        $station = $this->stationRepo->findOneBy(['stationCode' => $stationCode]);
        $associations = $this->aCSRepo->findBy(['station' => $station]);
        $lastHour=null;
        $lastHourDB=null;
        foreach ($associations as $asso) {
            $lastHourDB = $this->mesureRepo->findOneBy(['assocCapteurStation' => $asso],['dateTime' => 'DESC'])->getDateTime();
            if ($lastHourDB > $lastHour){
                $lastHour = $lastHourDB;
            }
        }
        return $lastHourDB;
    }
}