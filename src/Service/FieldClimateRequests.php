<?php

namespace App\Service;

use DateTime;
use App\Repository\StationRepository;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Service\StoreDataService;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

// service containing all functions specific to the Fieldclimate stations API
class FieldClimateRequests
{
    // Client to handle http request and hmac signature
    private $client;
    // Personal service that handles interaction with the database to store mesures in it
    private $storageManager;

    private $container;

    private $stationRepo;

    private $logger;


    public function __construct(HttpClientInterface $client, StoreDataService $storageManager,
        ContainerInterface $container, StationRepository $stationRepo, LoggerInterface $personalLogger)
    {
        $this->client = $client;
        $this->storageManager = $storageManager;
        $this->container = $container;
        $this->stationRepo = $stationRepo;
        $this->logger = $personalLogger;
    }
    
    // Takes an API request and the private/public keys as argument,
    // then handles connexion to the API, retrieval of the data and formatting to an array
    private function makeRequest ($publicKey, $privateKey, $request): array
    {
        $method = "GET";
        $timestamp = gmdate('D, d M Y H:i:s T');

        // Creating content to sign with private key
        $content_to_sign = $method.$request.$timestamp.$publicKey;

        // Hash content to sign into HMAC signature
        $signature = hash_hmac("sha256", $content_to_sign, $privateKey);

        // Add required headers
        // Authorization: hmac public_key:signature
        // Date: format 'D, d M Y H:i:s T'
        $headers = [
            "Accept: application/json",
            "Authorization: hmac $publicKey:$signature",
            "Date: $timestamp"
        ];
        
        // Indicate request type,
        // API url with request
        // and header embedded
        $response = $this->client->request(
            'GET',
            'https://api.fieldclimate.com/v1/'.$request,
            ['headers'=>$headers],
        );

        // get response as a string
        $content = $response->getContent();
        // put response into an array
        $content = $response->toArray();

        return $content;
    }

    // returns the request to get a list of the stations
    public function pullStations($publicKey, $privateKey): array
    {
        $request = '/user/stations';

        $content = $this->makeRequest($publicKey, $privateKey, $request);

        // $this->logger->info('catched my logger !');
        
        return $content;
    }


    // Verifies if the station exists
    public function doesStationExist($publicKey, $privateKey, $stationIdentifier): bool
    {
        $stationsArray = $this->pullStations($publicKey, $privateKey);
        $verif = false;
        foreach ($stationsArray as $station){
            if ($station["name"]["original"] == $stationIdentifier){
                $verif = true;
                break;
            }
        }
        return $verif;
    }

    // returns the request to get data from a station, 
    // provided its identifier code and the number of last hours requested
    public function pullData($publicKey, $privateKey, $stationIdentifier, $hours): array
    {
        $request = '/data/'.$stationIdentifier.'/raw/last/'.$hours;

        $content = $this->makeRequest($publicKey, $privateKey, $request);
        
        return $content;
    }

    // returns the request to gather the information the API can provide on a station
    public function pullStationInfo($publicKey, $privateKey, $stationIdentifier): array
    {
        $request = '/station/'.$stationIdentifier;

        $content = $this->makeRequest($publicKey, $privateKey, $request);
        
        return $content;
    }




    // function calling other functions to retrieve a list of the sensors available on a station
    // organized in a array
    // with the code :  <sensor reference for the station>_unit_<unit of the sensor>
    public function pullStationSensors($publicKey, $privateKey, $stationIdentifier): array
    {
        $response=[];
        $stationData = $this->pullData($publicKey, $privateKey, $stationIdentifier, '1');
        foreach ($stationData['sensors'] as $sensor){
            foreach ($sensor['aggr'] as $suffix => $value){
                $name = $sensor['name'].'_'.$suffix.'_unit_'.$sensor['unit'];
                $code=$sensor['ch'].'_'.$sensor['mac'].'_'.$sensor['serial'].'_'.$sensor['code'].'_'.$suffix;

                // !!! METHOD TO REWORK USED TO CHECK FOR DUPLICATE SENSORS !!!
                // 
                if (array_key_exists($name, $response) == true){
                    $name_bis = 'DOUBLON_'.$name;
                    if (array_key_exists($name_bis, $response) == true){
                        $name_bis = 'TRILON_'.$name;
                        if (array_key_exists($name_bis, $response) == true){
                            $name_bis = 'QUADRUPLON_'.$name;
                            if (array_key_exists($name_bis, $response) == true){
                                $name_bis = 'QUNTUPLON_'.$name;
                            }
                        }
                    }
                    $name = $name_bis;
                }
                // 
                // 

                $response[$name] = $code;
            }
        }
        return $response;
    }

    
    // function listing all sensor codes and names, from all FieldClimate stations,
    // organized in an array
    public function pullAllFCStationsSensors($publicKey, $privateKey): array
    {
        $rawdata = $this->pullStations($publicKey, $privateKey);
        
        foreach ($rawdata as $data){
            $id = $data['name']['original'];
            $response[$id]['name'] = $data['name']['custom'];
            $response[$id]['sensors'] = $this->pullStationSensors($publicKey, $privateKey, $id);
        }
        return $response;
    }

    // function iterating on the configuration of each station
    // to provide an array of stations grouped by identical sensor configuration
    public function pullFCModels($publicKey, $privateKey): array
    {
        $stations = $this->pullAllFCStationsSensors($publicKey, $privateKey);

        $response = [];
        $i=1;

        foreach ($stations as $station => $content){
            $model_exists=false;
            $model_ref='FC_'.$i;
            foreach ($response as $id => $model){
                if ($content['sensors'] == $model['sensors']){
                    $model_exists=true;
                    $model_ref=$id;
                    break;
                }
            }
            $response[$model_ref]['stations'][] = $station;
            if ($model_exists == false){
                $response[$model_ref]['sensors']=$content['sensors'];
                $i++;
            }            
        }
        return $response;
    }

    // function listing the sensor configuration of each model referenced
    // by the pullFCModels function
    public function pullFCAllSensors($publicKey, $privateKey): array
    {
        $models = $this->pullFCModels($publicKey, $privateKey);

        $response = [];

        foreach ($models as $modelName => $modelContent){
            foreach ($modelContent['sensors'] as $sensorName => $sensorCode){
                $response[$sensorName][$sensorCode][] = $modelName;
            }
        }

        return $response;
    }

    // function to verify that each sensor code matches only one sensor name
    // in the FieldClimate API
    public function checkFCSensorCodeUnicity($publicKey, $privateKey): array
    {
        $models = $this->pullFCModels($publicKey, $privateKey);

        $response = [];
        
        foreach ($models as $modelName => $modelContent){
            foreach ($modelContent['sensors'] as $sensorName => $sensorCode){
                $response[$sensorCode][$sensorName][] = $modelName;
            }
        }
        return $response;
    }


    // function to store mesures from a station passed as first argument
    // associated with the station identified by its API code as 2nd argument 
    // the data is the result of a get request based on the station identifier and the hours needed
    public function storeData($data, $stationIdentifier)
    {
        foreach ($data['data'] as $dataArray) {
            $dateTimeFromArray = $dataArray['date'];
            $dateTimeOriginal = \DateTime::createFromFormat("Y-m-d H:i:s", $dateTimeFromArray);

            // CONDITION TO ADD + 1 HOUR TO ALL MESURES TIMESTAMPING FROM H:30:00 TO H:59:59
            $getMinutes = (int)$dateTimeOriginal->format('i');
            if ($getMinutes >= 30){
                $dateTimeOriginal->add(new \DateInterval("PT1H"));
            }

            // ROUNDING DOWN TO H:00:00
            $dateToKeep = $dateTimeOriginal->format('Y-m-d H:00:00');
            $dateTime = \DateTime::createFromFormat("Y-m-d H:i:s", $dateToKeep);
            
            foreach ($dataArray as $codeCapteur => $valeur) {
                if ($codeCapteur != 'date'){
                    $this->storageManager->persistMesure($valeur, $stationIdentifier, $codeCapteur, $dateTime);
                }
            }
        }
    }

    

    
    // Test functions for planned service handling
    // Verifies dateTime of most recent mesure available on the API
        // Returns dateTime AS A STRING
    public function getLastHour($stationIdentifier)
    {
        $publicKey=$this->container->getParameter('api_fieldclimate_public_key');
        $privateKey=$this->container->getParameter('api_fieldclimate_private_key');

        $data = $this->pullData($publicKey, $privateKey, $stationIdentifier, 1);

        $lastDateTime = $data['data'][0]['date'];

        return $lastDateTime;
    }





    // Function to evaluate the number of hours missing from the database
    // With a margin of +2 hours to anticipate rounding up / actualization problems
    public function calculateHoursToPull($stationIdentifier)
    {
        
        $lastHourDB = null;
        $lastHourDB = $this->storageManager->getLastDateTimeInDB ($stationIdentifier);

        if ($lastHourDB != null){
            $lastHoutStation = new DateTime($this->getLastHour($stationIdentifier));

            $dateDifference = $lastHoutStation->diff($lastHourDB);
            $hoursDifference = (int)$dateDifference->format('%h') + (int)$dateDifference->format('%d')*24+2;
            return($hoursDifference);
        } else {
            return (100);
        }

        
    }




    // Function to store Data the missing mesures from a station in the database
    public function pullAndStoreMissingData($stationIdentifier)
    {
        $hours = $this->calculateHoursToPull($stationIdentifier);

        // dd($hours);

        $publicKey=$this->container->getParameter('api_fieldclimate_public_key');
        $privateKey=$this->container->getParameter('api_fieldclimate_private_key');

        $data = $this->pullData($publicKey, $privateKey, $stationIdentifier, $hours);

        // dd($data);

        $this->storeData($data, $stationIdentifier);
    }



    // function to automaticaly store data in everey station registered in the DB
    public function autoStoreMissingData()
    {
        $this->logger->info('auto storage function called');
        $stations = $this->stationRepo->findAll();
        foreach ($stations as $station) {
            $stationIdentifier = $station->getStationCode();

            
            $this->logger->info('Tries to store data for station code : '.$stationIdentifier);


                // NEW CODE
            $publicKey=$this->container->getParameter('api_fieldclimate_public_key');
            $privateKey=$this->container->getParameter('api_fieldclimate_private_key');
            $verif = $this->doesStationExist($publicKey, $privateKey, $stationIdentifier);
            if ( $verif == true){
                try{
                    if ($station->getConstructeur()->getConstructeurName() == 'FieldClimate'){
                        $this->pullAndStoreMissingData($stationIdentifier);
                    }
                }
                finally{

                }
            } else {
                $this->logger->error('The station '.$station->getStationName().' has a non existant API Code');
            }
            // if ( $stationIdentifier != null){
            //     try{
            //         if ($station->getConstructeur()->getConstructeurName() == 'FieldClimate'){
            //             $this->pullAndStoreMissingData($stationIdentifier);
            //         }
            //     }
            //     finally{

            //     }
            // }
        }
        return true;
    }



























    // Stores data in the DB
        // DEPRECATED
    public function tryPullAndStoreData($stationIdentifier, $hours)
    {

        $publicKey=$this->container->getParameter('api_fieldclimate_public_key');
        $privateKey=$this->container->getParameter('api_fieldclimate_private_key');

        $data = $this->pullData($publicKey, $privateKey, $stationIdentifier, $hours);

        $this->storeData($data, $stationIdentifier);
    }





    // function to automaticaly store data in everey station registered in the DB
        // DEPRECATED
    public function autoStoreData()
    {
        $hours = '90';
        $stations = $this->stationRepo->findAll();
        foreach ($stations as $station) {
            $stationIdentifier = $station->getStationCode();
            if ( $stationIdentifier != null){
                try{
                    if ($station->getConstructeur()->getConstructeurName() == 'FieldClimate'){
                        $this->tryPullAndStoreData($stationIdentifier, $hours);
                    }
                }
                finally{

                }
            }
        }
        return true;
    }



    // function to automaticaly store data in everey station registered in the DB
        // DEPRECATED
    public function autoStoreDataByHours($hours)
    {
        $stations = $this->stationRepo->findAll();
        foreach ($stations as $station) {
            $stationIdentifier = $station->getStationCode();
            if ( $stationIdentifier != null){
                try{
                    if ($station->getConstructeur()->getConstructeurName() == 'FieldClimate'){
                        $this->tryPullAndStoreData($stationIdentifier, $hours);
                    }
                }
                finally{

                }
            }
        }
    }


    

}