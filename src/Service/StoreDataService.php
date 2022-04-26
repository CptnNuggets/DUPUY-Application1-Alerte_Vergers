<?php

namespace App\Service;

use App\Entity\Mesure;
use App\Repository\AssocCapteurStationRepository;
use App\Repository\StationRepository;
use App\Repository\MesureRepository;
use Doctrine\ORM\EntityManagerInterface;

// General service containing the necessary functions to store mesures in the database
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



    // Function to store a mesure inside the database, arguments needed :
        // The mesure value (float)
        // the station API code (string)
        // the sensor API code (string)
        // the date-time of the mesure, rounded to the hour (datetime Y-m-d H:i:s)
    public function persistMesure ($valeur, $stationCode, $codeCapteur, $dateTime)
    {
        
        $station = $this->stationRepo->findOneBy(['stationCode' => $stationCode]);
        // $numeroCapteur = $this->aCSRepo->findOneBy(['station' => $station, 'codeCapteur' => $codeCapteur])->getNumeroCapteur();
        $aCS = $this->aCSRepo->findOneBy(['station' => $station, 'codeCapteur' => $codeCapteur]);

        // VERIFICATION THAT THE MESURE HAS NOT BEEN ALREADY ENTERED
        $existsAllready = $this->mesureRepo->findOneBy([
            // 'station' => $station,
            // 'numeroCapteur' => $numeroCapteur,
            'assocCapteurStation' => $aCS,
            'dateTime' => $dateTime
        ]);

        // MESURE ENTERED IN THE DB IF IT DOESNT EXISTS ALLREADY
        if ($existsAllready == null){
            $mesure = new Mesure;
            $mesure->setValeur($valeur);
            $mesure->setAssocCapteurStation($aCS);
            // $mesure->setStation($station);
            // $mesure->setNumeroCapteur($numeroCapteur);
            $mesure->setDateTime($dateTime);

            $this->em->persist($mesure);
            $this->em->flush();
        }
    }




    // Function to verify the last dateTime for mesures related to a station existing in the database
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