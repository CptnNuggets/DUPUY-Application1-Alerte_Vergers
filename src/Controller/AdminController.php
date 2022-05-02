<?php

namespace App\Controller;

use App\Entity\Constructeur;
use App\Entity\Mesure;
use App\Entity\NumeroCapteur;
use App\Entity\Station;
use App\Form\ConstructeurType;
use App\Form\MesureGeneratorType;
use App\Form\NumeroCapteurType;
use App\Repository\MesureRepository;
use App\Repository\NumeroCapteurRepository;
use App\Repository\StationRepository;
use App\Service\DataManipulation;
use App\Service\FieldClimateRequests;
use App\Service\MailerAlert;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

use function Symfony\Component\DependencyInjection\Loader\Configurator\env;

class AdminController extends AbstractController
{

    // ROUTE FOR POPULATING THE "numeroCapteur" Table, AND THE "Constructeur" TABLE
    // 
    #[Route('/admin', name: 'app_admin')]
    public function adminIndex(NumeroCapteurRepository $numeroCapteurRepository,
    Request $request, EntityManagerInterface $em): Response
    {
        $numcapteurs = $numeroCapteurRepository->findBy([],['numero' => 'ASC']);
        
        $newnumber = new NumeroCapteur;
        $formNC = $this->createForm(NumeroCapteurType::class, $newnumber);
        $formNC->handleRequest($request);

        if ($formNC->isSubmitted() && $formNC->isValid()) {
            $em->persist($newnumber);
            $em->flush();

            // $this->addFlash('New number added !');

            return $this->redirectToRoute('app_admin');

        }
        $newConstructeur = new Constructeur;
        $formCons = $this->createForm(ConstructeurType::class, $newConstructeur);
        $formCons->handleRequest($request);
        if ($formCons->isSubmitted() && $formCons->isValid()) {
            $em->persist($newConstructeur);
            $em->flush();

            // $this->addFlash('New constructeur added !');

            return $this->redirectToRoute('app_admin');
        }

        return $this->renderForm('admin/index.html.twig', 
            compact('numcapteurs', 'formNC', 'formCons'));
    }



    // TO REMOVE
        // CREATION OF GARBAGE DATA
    #[Route('/admin/mesures', name: 'app_admin_mesures')]
    public function adminMesureGenerator(MesureRepository $mesureRepository,
    Request $request, EntityManagerInterface $em): Response
    {
        $mesure = new Mesure;
        $form = $this->createForm(MesureGeneratorType::class, $mesure);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($mesure);
            $em->flush();
            return $this->redirectToRoute('app_admin_mesures');

        }

        return $this->renderForm('admin/mesures.html.twig', compact('form'));
    }




    // THREE GENERIC ROUTES TO ACCESS VARIOUS INFORMATIONS ON THE FIELDCLIMATE API
    // 
    // 
    #[Route('/admin/pullData', name: 'app_admin_pullData')]
    public function adminPullData(FieldClimateRequests $fieldClimateRequest): Response
    {
        $hours='12';
        $stationIdentifier='00000AB7';
        $publicKey=$this->getParameter('api_fieldclimate_public_key');
        $privateKey=$this->getParameter('api_fieldclimate_private_key');
        
        $response = $fieldClimateRequest->pullData($publicKey, $privateKey, $stationIdentifier, $hours);

        dd($response);
    }

    #[Route('/admin/pullStations', name: 'app_admin_pullStations')]
    public function adminPullStations(FieldClimateRequests $fieldClimateRequest): Response
    {
        $publicKey=$this->getParameter('api_fieldclimate_public_key');
        $privateKey=$this->getParameter('api_fieldclimate_private_key');
        
        $response = $fieldClimateRequest->pullStations($publicKey, $privateKey);

        dd($response);
    }

    #[Route('/admin/pullStationInfo', name: 'app_admin_pullStationInfo')]
    public function adminStationInfo(FieldClimateRequests $fieldClimateRequest): Response
    {
        $stationIdentifier='00000AB7';
        $publicKey=$this->getParameter('api_fieldclimate_public_key');
        $privateKey=$this->getParameter('api_fieldclimate_private_key');
        
        $response = $fieldClimateRequest->pullStationInfo($publicKey, $privateKey, $stationIdentifier);

        dd($response);
    }




    
    // ROUTE TO GET THE CONFIGURATION OF ALL FIELDCLIMATE STATIONS
    // 
    #[Route('/admin/pullAllFCStationsSensors', name: 'app_admin_pullAllFCStations')]
    public function adminpullAllFCStationsSensors(FieldClimateRequests $fieldClimateRequest): Response
    {
        $publicKey=$this->getParameter('api_fieldclimate_public_key');
        $privateKey=$this->getParameter('api_fieldclimate_private_key');
        

        $response=$fieldClimateRequest->pullAllFCStationsSensors($publicKey, $privateKey);

        dd($response);
    }



    // USELESS AFTER REWORK OF THE DATABASE
            // ROUTE TO OBTAIN THE TYPICAL FIELCLIMATE MODELS
            // 
    #[Route('/admin/fCModels', name: 'app_admin_pullFCModels')]
    public function adminpullFCModels(FieldClimateRequests $fieldClimateRequest): Response
    {
        $publicKey=$this->getParameter('api_fieldclimate_public_key');
        $privateKey=$this->getParameter('api_fieldclimate_private_key');
        

        $response=$fieldClimateRequest->pullFCModels($publicKey, $privateKey);

        dd($response);
    }




    // ROUTE TO GET ALL THE SENSORS EXISTING ON THE FIELDCLIMATE API
    // 
    #[Route('/admin/fCSensors', name: 'app_admin_pullFCSensors')]
    public function adminpullFCAllSensors(FieldClimateRequests $fieldClimateRequest): Response
    {
        $publicKey=$this->getParameter('api_fieldclimate_public_key');
        $privateKey=$this->getParameter('api_fieldclimate_private_key');
        

        $response=$fieldClimateRequest->pullFCAllSensors($publicKey, $privateKey);

        dd($response);
    }



    // ROUTE TO CHECK THE UNICITY OF EACH FIELDCLIMATE API SENSOR CODE
    // 
    #[Route('/admin/fCSensorsUnicity', name: 'app_admin_checkFCSensorsUnicity')]
    public function admincheckFCSensorCodeUnicity(FieldClimateRequests $fieldClimateRequest): Response
    {
        $publicKey=$this->getParameter('api_fieldclimate_public_key');
        $privateKey=$this->getParameter('api_fieldclimate_private_key');
        

        $response=$fieldClimateRequest->checkFCSensorCodeUnicity($publicKey, $privateKey);

        dd($response);
    }



    // ROUTE TO PULL DATA FROM FIELDCLIMATE STATION BY ID AND NUMBER OF HOURS REQUESTED
    // 
    #[Route('/admin/pullFCData/{stationIdentifier<[0-9a-fA-F]{8}>}/{hours<[0-9]{1,2}>}', name: 'app_admin_pullDataFC')]
    public function adminPullFCData(FieldClimateRequests $fieldClimateRequest, string $stationIdentifier, string $hours): Response
    {
        $publicKey=$this->getParameter('api_fieldclimate_public_key');
        $privateKey=$this->getParameter('api_fieldclimate_private_key');
        
        $response = $fieldClimateRequest->pullData($publicKey, $privateKey, $stationIdentifier, $hours);

        dd($response);
    }






    // 
    // DEV FUNCTIONS RELATED TO MESURES
    // 


    // FUNCTION USED TO STORE MESURES IN THE DATABASE
    // BASED ON AN API STATION CODE AND A NUMBER OF HOURS TO PULL FROM THE API
    // 
    #[Route('/admin/storeData/FC/{stationIdentifier<[0-9a-fA-F]{8}>}/{hours<[0-9]{1,3}>}', name: 'app_admin_storeData_FC')]
    public function storeDataFC(FieldClimateRequests $fieldClimateRequest, string $stationIdentifier, string $hours): Response
    {
        $publicKey=$this->getParameter('api_fieldclimate_public_key');
        $privateKey=$this->getParameter('api_fieldclimate_private_key');
        
        $data = $fieldClimateRequest->pullData($publicKey, $privateKey, $stationIdentifier, $hours);

        $fieldClimateRequest->storeData($data, $stationIdentifier);

        dd($data);
    }





    // ROUTE TO TEST NEW FUNCTIONS
    // 
    #[Route('/admin/test/pullAndStoreMissingData', name: 'app_admin_test_pullAndStoreMissingData')]
    public function adminTestPullMissingData(DataManipulation $dataManip, MailerAlert $mailerAlert,
        StationRepository $stationRepo, MesureRepository $mesureRepo, FieldClimateRequests $fC) : Response
    {
        $stationCode='00000AB7';

        $fC->pullAndStoreMissingData($stationCode);


        return $this->render('layouts/base.html.twig');

        
        

    }


    // FUNCTION TO SEND ALERT EMAILS TO CONTACT ADRESSES OF VERGERS
    // BASED ON A CHECK OF THE LAST 12 HOURS OF DATA FROM A STATION
    // AND A PHONY MATHS CHECK
    // 
    #[Route('/admin/test/math', name: 'app_admin_test_math')]
    public function adminTestMath(DataManipulation $dataManip, MailerAlert $mailerAlert,
        StationRepository $stationRepo) : Response
    {
        $stationCode='00000AB7';
        $hours = 12;
        
        $result = $dataManip->getDataFromDB($stationCode, $hours);

        dd($result);
        // $vergers[] = 

        // $alert = $dataManip->verifyMathsModel($result);


        // if ($alert == true){
        //     $station = $stationRepo->findOneBy(['stationCode' => $stationCode]);
        //     $aSV = $station->getAssocStationVergers();
            
        //     foreach ($aSV as $asso) {
        //         $contacts[] = $asso->getVerger()->getContact();
        //     }
        //     foreach ($contacts as $address) {
        //         $mailerAlert->sendAlert($address);
        //     }
        // }

        return $this->render('layouts/base.html.twig');

    }




        // ROUTE TO TEST NEW FUNCTIONS
    // 
    #[Route('/admin/test', name: 'app_admin_test')]
    public function adminTest(DataManipulation $dataManip, MailerAlert $mailerAlert,
        StationRepository $stationRepo, MesureRepository $mesureRepo, FieldClimateRequests $fC) : Response
    {
        // $stationCode='00000AB7';

        // $fC->pullAndStoreMissingData($stationCode);

        $dataManip->determineActiveAlertLevels();


        return $this->render('layouts/base.html.twig');


    }



    

    // FUNCTION TO SEND ALERT EMAILS TO CONTACT ADRESSES OF VERGERS
        // BASED ON A CHECK OF THE LAST 12 HOURS OF DATA FROM A STATION
        // AND A PHONY MATHS CHECK
        // 
    #[Route('/admin/test/email', name: 'app_admin_test_email')]
    public function adminTestEmail(DataManipulation $dataManip, MailerAlert $mailerAlert,
        StationRepository $stationRepo) : Response
    {
        $mailerAlert->sendAlertEmails();
        return $this->render('layouts/base.html.twig');      
        
    }








       // ROUTE TO TEST NEW FUNCTIONS
    // 
    #[Route('/admin/debugstorage', name: 'app_admin_debugstorage')]
    public function adminDebugStorage(DataManipulation $dataManip, MailerAlert $mailerAlert,
        StationRepository $stationRepo, MesureRepository $mesureRepo, FieldClimateRequests $fC) : Response
    {
        // $stationCode='00000AB7';

        // $fC->pullAndStoreMissingData($stationCode);

        $fC->autoStoreMissingData();


        return $this->render('layouts/base.html.twig');

        
        

    }




        // ROUTE TO TEST NEW FUNCTIONS
        // 
        #[Route('/admin/test/display_sensor_array', name: 'app_admin_test_displaySensorArray')]
        public function adminDisplaySensorArray(DataManipulation $dataManip, MailerAlert $mailerAlert,
            StationRepository $stationRepo, MesureRepository $mesureRepo, FieldClimateRequests $fC) : Response
        {
            $stationCode='0020424C';

            $station = $stationRepo->findOneBy(['stationCode' => $stationCode]);

            $sensors = $station->getListeCapteurs();

            dd($sensors);


            return $this->render('layouts/base.html.twig');

            
            

        }


}
