<?php

namespace App\Service;

use App\Repository\CapteurPourMathsRepository;
use App\Repository\MesureRepository;
use App\Repository\StationRepository;
use Psr\Log\LoggerInterface;

    // Service used for MANIPULATING THE MATH MODEL

class DataManipulation
{
        // Repository for Mesures
    private $mesureRepo;
        // Repository for Stations
    private $stationRepo;
        // Repository for Capteurs pour Maths
    private $cpmRepo;
        // Logger Interface outputting in a personal logger
    private $logger;



    public function __construct(
        MesureRepository $mesureRepo, StationRepository $stationRepo, CapteurPourMathsRepository $cpmRepo, LoggerInterface $personalLogger
        )
    {
        $this->mesureRepo = $mesureRepo;
        $this->stationRepo = $stationRepo;
        $this->cpmRepo = $cpmRepo;
        $this->logger = $personalLogger;
    }



    // function for PULLING MESURES from the db for all SENSORS OF A STATION,
        // Arguments :  Station Api Code, Number of hours of mesure requested
        // Returns an array presenting :    [sensor name] => [ [dateTime] => value ]

    public function getDataFromDB($stationIdentifier, $hours)
    {
        $station = $this->stationRepo->findOneBy(['stationCode' => $stationIdentifier]);

        $capteurs=[];
        foreach ($station->getAssocCapteurStations() as $assoc){
            $capteurName = $assoc->getCapteur()->getCapteurName();
            
                // Calls a personal function from the Mesures Repository
                // Giving the $hours last mesures for a given $station and $numeroCapteur
            $mesures = $this->mesureRepo->findByStationAndCapteur($assoc, $hours);

            foreach ($mesures as $mesure) {
                $dateTime = $mesure->getDateTime()->format('Y-m-d H:i:s');
                $capteurs[$capteurName][$dateTime] = $mesure->getValeur();
            }

                // Sorts by increasing dateTime
            ksort($capteurs[$capteurName]);
        }
        return($capteurs);
         
    }


        // function for PULLING THE MATH MODEL MESURES from the DB for a station
            // Arguments :  Station Api Code, Number of hours of mesure requested
            // Returns an array presenting :    [sensor name] => [ [dateTime] => value ]
    public function getMathDataFromDB($stationIdentifier, $hours)
    {
        $station = $this->stationRepo->findOneBy(['stationCode' => $stationIdentifier]);

        $airTemperatureCapteur = $this->cpmRepo->findOneBy(['nomRaccourci' => 'tempAir'])->getCapteur();
        $humectationCapteur = $this->cpmRepo->findOneBy(['nomRaccourci' => 'humec'])->getCapteur();

        $airTemperatureName = $airTemperatureCapteur->getCapteurName();
        $humectationName = $humectationCapteur->getCapteurName();

        $capteurs=[];
        foreach ($station->getAssocCapteurStations() as $assoc){
            $capteur = $assoc->getCapteur();
            if ($capteur == $airTemperatureCapteur || $capteur == $humectationCapteur){
                $mesures = $this->mesureRepo->findByStationAndCapteur($assoc, $hours);
                foreach ($mesures as $mesure) {
                    $dateTime = $mesure->getDateTime()->format('Y-m-d H:i:s');
                    $capteurs[$capteur->getCapteurName()][$dateTime] = $mesure->getValeur();
                }
                ksort($capteurs[$capteur->getCapteurName()]);
            }
        }
            // Returns mesures ONLY if both usefull sensors are present
        if (array_key_exists($humectationName, $capteurs) && array_key_exists($airTemperatureName, $capteurs) ){
            return($capteurs); 
        } else {
                // otherwise returns NULL
            return null;
        }
                    
    }





        //                      ***
        //     /!\    CURRENTLY IN TESTING MODE     /!\
        //                      ***

        // function that COMPARES DATA to the MATHEMATICAL MODEL and determines the ALERT LEVEL
            // Returns an array of [ verger_id => vergerName, contact, riskCode]
            // Containing the alerts to broadcast

    public function determineActiveAlertLevels (){
            // Array to return
        $alerts=[];

        $stations = $this->stationRepo->findAll();

        $airTemperatureCapteur = $this->cpmRepo->findOneBy(['nomRaccourci' => 'tempAir'])->getCapteur();
        $humectationCapteur = $this->cpmRepo->findOneBy(['nomRaccourci' => 'humec'])->getCapteur();

        $airTemperatureName = $airTemperatureCapteur->getCapteurName();
        $humectationName = $humectationCapteur->getCapteurName();


        foreach ($stations as $station){

                // Array of vergers infos related to this station organised as follow:
                // [id (uuid)] => ["alertCode"], ["contact"], ["vergerName"]
            $vergersAndRisks = [];            
            
            $assosVergers = $station->getAssocStationVergers();
            foreach ($assosVergers  as $asso){
                $alertCode = $asso->getVerger()->getMessageAlerte()->getAlerteCode();
                $contact = $asso->getVerger()->getContact();
                $vergerName = $asso->getVerger()->getIdVerger();
                $vergersAndRisks[$asso->getVerger()->getId()->__toString()]["alertCode"] = $alertCode;
                $vergersAndRisks[$asso->getVerger()->getId()->__toString()]["contact"] = $contact;
                $vergersAndRisks[$asso->getVerger()->getId()->__toString()]["vergerName"] = $vergerName;
            }



            // //      /!\               /!\
            // // REAL WAY TO OBTAIN THE MESURES : UNCOMMENT 
            // $stationCode = $station->getStationCode();
            // $mesuresH = $this->getMathDataFromDB($stationCode, 4);
            
            

            
            //    /!\               /!\
            // TESTING MESURES : TO COMMENT IF REAL MESURES ARE TO BE USED
                // Possible cases :
                        // "insignificant humec"
                        // "low humec low temp"
                        // "medium humec low temp"
                        // "medium humec high temp"
                        // "high humec high temp"
                        // "worse case"    
            $case = "medium humec low temp";
            $mesuresH = $this->getTestingMesures($airTemperatureName, $humectationName, 4, $case);


            


                // the usefull sensors are present
            if ($mesuresH != null){
                    // Condition to verify for alerting (humectation != 0 in the last 4 hours)
                    
                    // Cumulative Humectation on the last 4 hours
                    $H = 0;
                    foreach ($mesuresH[$humectationName] as $humectation){
                        $H += $humectation;
                    }
                    
                if ($H != 0) {
                    // POTENTIAL RISK
                    // DETERMINES THE SECOND RELEVANT MESURE
                        // Cumulative air Temperature on the last 6 hours
                    $T = 0;




                    // //      /!\               /!\
                    // // REAL WAY TO OBTAIN THE MESURES : UNCOMMENT
                    // $mesuresT = $this->getMathDataFromDB($stationCode, 6);
                    
                    
                    
                    //    /!\               /!\
                    // TESTING MESURES : TO COMMENT IN REAL CONDITIONS
                    $mesuresT = $this->getTestingMesures($airTemperatureName, $humectationName, 6, $case);


                    foreach($mesuresT[$airTemperatureName] as $temperature){
                        $T += $temperature;
                    }

                        // DETERMINES the LOW, MEDIUM and HIGH RISK AREAS based on the TEMPERATURE
                    $lowRisk = 0.67*$T + 12.34;
                    $mediumRisk = 0.058*$T**2 - 0.12*$T + 6.78;
                    $highRisk = 0.0034*$T**3 - 0.009*$T**2 + 0.23*$T;

                        // ADDS the Verger, Risk Level and Contact to the RETURNED ARRAY if the risk level is above the desired level for that verger
                    foreach ($vergersAndRisks as $id => $array){
                        if ($H > $highRisk){
                            $alerts[$id]["vergerName"] = $array["vergerName"];
                            $alerts[$id]["contact"] = $array["contact"];
                            $alerts[$id]["riskCode"] = "highRisk";
                        }
                        else if ($H > $mediumRisk){
                            if ($array["alertCode"] == "mediumRisk" || $array["alertCode"] == "lowRisk"){
                                $alerts[$id]["vergerName"] = $array["vergerName"];
                                $alerts[$id]["contact"] = $array["contact"];
                                $alerts[$id]["riskCode"] = "mediumRisk";
                            }
                        } else if ($H > $lowRisk) {
                            if ($array["alertCode"] == "lowRisk"){
                                $alerts[$id]["vergerName"] = $array["vergerName"];
                                $alerts[$id]["contact"] = $array["contact"];
                                $alerts[$id]["riskCode"] = "lowRisk";
                            }
                        }
                    }

                } else {
                    // NO RISK : do nothing
                }
            } else{
                $this->logger->warning('The station '.$station->getStationName().' has missing sensors !');
            }
        }
        return $alerts;
    }




    // TESTING FUNCTION : proposes a data array equivalent to what would be pulled from the database
    
    public function getTestingMesures($airTemperatureName, $humectationName, $hours, $case){
        $mesures = [];

        if ($case == "insignificant humec"){
            $mesures[$humectationName]["2022-04-21 09:00:00"] = 0.0;
            $mesures[$humectationName]["2022-04-21 08:00:00"] = 1.2;
            $mesures[$humectationName]["2022-04-21 07:00:00"] = 2.3;
            $mesures[$humectationName]["2022-04-21 06:00:00"] = 0.0;
            $mesures[$humectationName]["2022-04-21 05:00:00"] = 0.0;
            $mesures[$humectationName]["2022-04-21 04:00:00"] = 0.0;
            $mesures[$humectationName]["2022-04-21 03:00:00"] = 0.0;
            $mesures[$humectationName]["2022-04-21 02:00:00"] = 1.4;
            $mesures[$humectationName]["2022-04-21 01:00:00"] = 0.0;
            $mesures[$humectationName]["2022-04-21 00:00:00"] = 0.0;
            $mesures[$humectationName]["2022-04-20 23:00:00"] = 0.0;
    
            $mesures[$airTemperatureName]["2022-04-21 09:00:00"] = 5.98;
            $mesures[$airTemperatureName]["2022-04-21 08:00:00"] = 3.26;
            $mesures[$airTemperatureName]["2022-04-21 07:00:00"] = 2.49;
            $mesures[$airTemperatureName]["2022-04-21 06:00:00"] = 2.49;
            $mesures[$airTemperatureName]["2022-04-21 05:00:00"] = 2.48;
            $mesures[$airTemperatureName]["2022-04-21 04:00:00"] = 3.8;
            $mesures[$airTemperatureName]["2022-04-21 03:00:00"] = 4.94;
            $mesures[$airTemperatureName]["2022-04-21 02:00:00"] = 4.77;
            $mesures[$airTemperatureName]["2022-04-21 01:00:00"] = 6.35;
            $mesures[$airTemperatureName]["2022-04-21 00:00:00"] = 7.3;
            $mesures[$airTemperatureName]["2022-04-20 23:00:00"] = 8.73;
        }
        else if ($case == "low humec low temp"){
            $mesures[$humectationName]["2022-04-21 15:00:00"] = 2.0;
            $mesures[$humectationName]["2022-04-21 14:00:00"] = 10.2;
            $mesures[$humectationName]["2022-04-21 13:00:00"] = 7.3;
            $mesures[$humectationName]["2022-04-21 12:00:00"] = 11.0;
            $mesures[$humectationName]["2022-04-21 11:00:00"] = 7.3;
            $mesures[$humectationName]["2022-04-21 10:00:00"] = 1.2;
    
            $mesures[$airTemperatureName]["2022-04-21 15:00:00"] = 6.98;
            $mesures[$airTemperatureName]["2022-04-21 14:00:00"] = 4.26;
            $mesures[$airTemperatureName]["2022-04-21 13:00:00"] = 3.49;
            $mesures[$airTemperatureName]["2022-04-21 12:00:00"] = 3.49;
            $mesures[$airTemperatureName]["2022-04-21 11:00:00"] = 3.48;
            $mesures[$airTemperatureName]["2022-04-21 10:00:00"] = 4.8;
        }
        else if ($case == "medium humec low temp"){
            $mesures[$humectationName]["2022-04-21 15:00:00"] = 12.0;
            $mesures[$humectationName]["2022-04-21 14:00:00"] = 15.2;
            $mesures[$humectationName]["2022-04-21 13:00:00"] = 17.3;
            $mesures[$humectationName]["2022-04-21 12:00:00"] = 11.0;
            $mesures[$humectationName]["2022-04-21 11:00:00"] = 7.3;
            $mesures[$humectationName]["2022-04-21 10:00:00"] = 1.2;
    
            $mesures[$airTemperatureName]["2022-04-21 15:00:00"] = 6.98;
            $mesures[$airTemperatureName]["2022-04-21 14:00:00"] = 4.26;
            $mesures[$airTemperatureName]["2022-04-21 13:00:00"] = 3.49;
            $mesures[$airTemperatureName]["2022-04-21 12:00:00"] = 3.49;
            $mesures[$airTemperatureName]["2022-04-21 11:00:00"] = 3.48;
            $mesures[$airTemperatureName]["2022-04-21 10:00:00"] = 4.8;
        }
        else if ($case == "medium humec high temp"){
            $mesures[$humectationName]["2022-04-21 15:00:00"] = 12.0;
            $mesures[$humectationName]["2022-04-21 14:00:00"] = 15.2;
            $mesures[$humectationName]["2022-04-21 13:00:00"] = 17.3;
            $mesures[$humectationName]["2022-04-21 12:00:00"] = 11.0;
            $mesures[$humectationName]["2022-04-21 11:00:00"] = 7.3;
            $mesures[$humectationName]["2022-04-21 10:00:00"] = 1.2;
    
            $mesures[$airTemperatureName]["2022-04-21 15:00:00"] = 25.98;
            $mesures[$airTemperatureName]["2022-04-21 14:00:00"] = 24.26;
            $mesures[$airTemperatureName]["2022-04-21 13:00:00"] = 23.49;
            $mesures[$airTemperatureName]["2022-04-21 12:00:00"] = 23.49;
            $mesures[$airTemperatureName]["2022-04-21 11:00:00"] = 23.48;
            $mesures[$airTemperatureName]["2022-04-21 10:00:00"] = 24.8;
        }
        else if ($case == "high humec high temp"){
            $mesures[$humectationName]["2022-04-21 15:00:00"] = 27.0;
            $mesures[$humectationName]["2022-04-21 14:00:00"] = 35.2;
            $mesures[$humectationName]["2022-04-21 13:00:00"] = 27.3;
            $mesures[$humectationName]["2022-04-21 12:00:00"] = 24.0;
            $mesures[$humectationName]["2022-04-21 11:00:00"] = 17.3;
            $mesures[$humectationName]["2022-04-21 10:00:00"] = 1.2;
    
            $mesures[$airTemperatureName]["2022-04-21 15:00:00"] = 25.98;
            $mesures[$airTemperatureName]["2022-04-21 14:00:00"] = 24.26;
            $mesures[$airTemperatureName]["2022-04-21 13:00:00"] = 23.49;
            $mesures[$airTemperatureName]["2022-04-21 12:00:00"] = 23.49;
            $mesures[$airTemperatureName]["2022-04-21 11:00:00"] = 23.48;
            $mesures[$airTemperatureName]["2022-04-21 10:00:00"] = 24.8;
        }
        else if ($case == "worse case"){
            $mesures[$humectationName]["2022-04-21 15:00:00"] = 27.0;
            $mesures[$humectationName]["2022-04-21 14:00:00"] = 35.2;
            $mesures[$humectationName]["2022-04-21 13:00:00"] = 27.3;
            $mesures[$humectationName]["2022-04-21 12:00:00"] = 24.0;
            $mesures[$humectationName]["2022-04-21 11:00:00"] = 17.3;
            $mesures[$humectationName]["2022-04-21 10:00:00"] = 1.2;
    
            $mesures[$airTemperatureName]["2022-04-21 15:00:00"] = 3.98;
            $mesures[$airTemperatureName]["2022-04-21 14:00:00"] = 4.26;
            $mesures[$airTemperatureName]["2022-04-21 13:00:00"] = 3.49;
            $mesures[$airTemperatureName]["2022-04-21 12:00:00"] = 3.49;
            $mesures[$airTemperatureName]["2022-04-21 11:00:00"] = 3.48;
            $mesures[$airTemperatureName]["2022-04-21 10:00:00"] = 4.8;
        }
        else {
            $mesures[$humectationName]["2022-04-21 15:00:00"] = 34.0;
            $mesures[$humectationName]["2022-04-21 14:00:00"] = 42.2;
            $mesures[$humectationName]["2022-04-21 13:00:00"] = 27.3;
            $mesures[$humectationName]["2022-04-21 12:00:00"] = 27.0;
            $mesures[$humectationName]["2022-04-21 11:00:00"] = 17.3;
            $mesures[$humectationName]["2022-04-21 10:00:00"] = 1.2;
    
            $mesures[$airTemperatureName]["2022-04-21 15:00:00"] = 7.98;
            $mesures[$airTemperatureName]["2022-04-21 14:00:00"] = 12.26;
            $mesures[$airTemperatureName]["2022-04-21 13:00:00"] = 8.49;
            $mesures[$airTemperatureName]["2022-04-21 12:00:00"] = 7.49;
            $mesures[$airTemperatureName]["2022-04-21 11:00:00"] = 5.48;
            $mesures[$airTemperatureName]["2022-04-21 10:00:00"] = 4.8;
        }
        

        $mesuresToReturn = [];
        $i = 0;
        foreach ($mesures[$humectationName] as $dateTime => $mesure){
            if ($i < $hours){
                $mesuresToReturn[$humectationName][$dateTime] = $mesure;
                $i++;
            } else {
                break;
            }
        }
        ksort($mesuresToReturn[$humectationName]);

        $i = 0;
        foreach ($mesures[$airTemperatureName] as $dateTime => $mesure){
            if ($i < $hours){
                $mesuresToReturn[$airTemperatureName][$dateTime] = $mesure;
                $i++;
            } else {
                break;
            }
        }
        ksort($mesuresToReturn[$airTemperatureName]);

        return $mesuresToReturn;

    }










}