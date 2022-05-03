<?php

namespace App\Controller;

use App\Entity\AssocCapteurStation;
use App\Entity\Capteur;
use App\Entity\Station;
use App\Form\AssocCapteurStationType;
use App\Form\StationType;
use App\Repository\AssocCapteurStationRepository;
use App\Repository\CapteurRepository;
use App\Repository\NumeroCapteurRepository;
use App\Repository\StationRepository;
use App\Service\FieldClimateRequests;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\CodePointString;
use Symfony\Component\Validator\Constraints\Length;

class StationController extends AbstractController
{
        // INDEX ROUTE TO LIST EXISTING STATIONS AND FORM TO CREATE A NEW ONE
    
    #[Route('/stations', name: 'app_stations_list', methods:'GET|POST')]
    public function listStations(StationRepository $stationRepository,
            Request $request, EntityManagerInterface $em): Response
    {
        $stations = $stationRepository->findBy([],['id' => 'ASC']);

        $station = new Station;
        $form = $this->createForm(StationType::class, $station);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($station);
            $em->flush();

            $this->addFlash('success','Station '.$station->getStationName().' créée avec succès !');

            return $this->redirectToRoute('app_stations_list');

        }

        return $this->renderForm('entities/stations/index.html.twig', compact('stations', 'form'));
    }




        // route to the VIEW allowing MODIFICATION of a STATION which contains :
            // a form allowing to modify the station information
            // a function to add a new sensor (by creating a new AssocCapteurStation entity)
            // a list of sensors registered for that station, allowing modification and deletion
    
    #[Route('/stations/edit/{id<[0-9a-fA-F]{8}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{12}>}', 
        name: 'app_stations_edit', methods:'GET|POST')]
    public function editStation(EntityManagerInterface $em, Station $station, Request $request,
        NumeroCapteurRepository $numCapteurRepo)
    {
            // Lists the associations with sensors related to the station
        $associations = $station->getAssocCapteurStations();


            // Extracts the numbers attached to each sensors
        $numCapteurs = [];
        foreach ($associations as $association) {
            $numCapteurs[] = $association->getNumeroCapteur()->getNumero();
        }
        $orderedNumCapteurs = array_values($numCapteurs);
        asort($orderedNumCapteurs);
        

            // Determins the next available number for an extra sensor that would be added
        $numero=0;
        foreach ($orderedNumCapteurs as $key => $value) {
            if($numero+1 == $value){
                $numero=$value;
            }
            else{
                break;
            }
        }
        $numero+=1;    
            // Consults the Repo of the table numeroCapteur to fetch the right entity to use for the next association
        $numeroCapteur = $numCapteurRepo->findOneBy(['numero' => $numero]);


            // Form to create a new capteurStation association
        $asso = new AssocCapteurStation;
        $asso->setStation($station);
        $asso->setNumeroCapteur($numeroCapteur);
        $formCapteur = $this->createForm(AssocCapteurStationType::class, $asso);
        $formCapteur->handleRequest($request);

        if ($formCapteur->isSubmitted() && $formCapteur->isValid()) {
            $em->persist($asso);
            $em->flush();

            $this->addFlash('success','Capteur '.$asso->getCapteur()->getCapteurName().' attribué avec succès au numéro '.$numero.' sur cette station.');

            return $this->redirectToRoute('app_stations_edit', ['id' => $station->getId()]);
        }


            // Form to modify the station informations
        $formStation = $this->createForm(StationType::class, $station);
        $formStation->handleRequest($request);
        if ($formStation->isSubmitted() && $formStation->isValid()){

            $em->flush();

            $this->addFlash('success','La station '.$station->getStationName().' a été modifiée !');

            return $this->redirectToRoute('app_stations_list');
        }

        return $this->renderForm('entities/stations/edit.html.twig',
            compact('station', 'formStation', 'associations', 'formCapteur', 'numero', 'orderedNumCapteurs'));

    }






        // route called when DELETING a STATION
     
    #[Route('/stations/delete/{id<[0-9a-fA-F]{8}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{12}>}', 
        name: 'app_stations_delete', methods:'GET|POST')]
    public function deleteStation(EntityManagerInterface $em, Station $station)
    {
        $em->remove($station);
        $em->flush();

        $this->addFlash('info', 'La station '.$station->getStationName().' a été supprimée !');

        return $this->redirectToRoute('app_stations_list');
    }





    // route to MODIFY a SENSOR
        // (by upgrading the AssocCapteurStation)
     
    #[Route('/stations/edit_capteur/{id<[0-9a-fA-F]{8}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{12}>}', name: 'app_stations_editCapteur')]
    public function editStationCapteur(Request $request, EntityManagerInterface $em, AssocCapteurStation $association) : Response
    {
        $form = $this->createForm(AssocCapteurStationType::class, $association);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success','Le capteur n°'.$association->getNumeroCapteur()->getNumero().' a été modifié');

            return $this->redirectToRoute('app_stations_edit', ['id' => $association->getStation()->getId()]);
        }

        return $this->renderForm('entities/stations/editCapteur.html.twig', compact('association', 'form' ));

    }




        // route called to DELETE a SENSOR (by deleting the AssocCapteurStation entity)
     
    #[Route('/stations/delete_capteur/{id<[0-9a-fA-F]{8}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{12}>}', name: 'app_stations_deleteCapteur', methods:'GET|PUT|POST')]
    public function deleteStationModelCapteur(EntityManagerInterface $em, AssocCapteurStation $aCS) : Response
    {
        $stationId = $aCS->getStation()->getId();

        $em->remove($aCS);
        $em->flush();

        $this->addFlash('info','Le capteur n°'.$aCS->getNumeroCapteur()->getNumero().' a été supprimé !');

        return $this->redirectToRoute('app_stations_edit', ['id' => $stationId]);

    }
    




        // route called to AUTOCONFIGURE a FIELDCLIMATE STATION 

    #[Route('/stations/addFieldClimate', name: 'app_stations_addFieldClimate')]
    public function configureFCSensors(Request $request, FieldClimateRequests $fieldClimateRequest, 
        EntityManagerInterface $em, StationRepository $stationRepo) : Response
    {
            // Form of creation of the new station
        $station = new Station;
        $form = $this->createForm(StationType::class, $station);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

                // Extraction of the API code entered by the user
            $stationCode = $station->getStationCode();
            $publicKey=$this->getParameter('api_fieldclimate_public_key');
            $privateKey=$this->getParameter('api_fieldclimate_private_key');

            $verif=$fieldClimateRequest->doesStationExist($publicKey, $privateKey, $stationCode);

            if ($verif == true){
                    // Pulls the sensors for that station from the API
                $sensors = $fieldClimateRequest->pullStationSensors($publicKey, $privateKey, $stationCode);

                if ($sensors != null){

                        // Array containing the sensors, passed as an array into the DB as parameter of the station
                    $station->setListeCapteurs($sensors);

                        // Persistence of the station inside the DB
                    $em->persist($station);
                    $em->flush();
                    $id=$station->getId();

                    $this->addFlash('success','Station '.$station->getStationName().' créée avec le code API '.$station->getStationCode().', configurez ses capteurs.');

                    return $this->redirectToRoute('app_stations_addFieldClimate_sensors', compact('id'));
                }
            } else {
                $this->addFlash('error','Ce code station n\'existe pas');
            }

        }
        return $this->renderForm('entities/stations/importFC.html.twig', compact('form'));
    }




        // route called to CONFIGURE THE SENSORS of an IMPORTED FIELDCLIMATE STATION
            
    #[Route('/stationModels/configureFieldClimate/{id<[0-9a-fA-F]{8}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{12}>}', 
            name: 'app_stations_addFieldClimate_sensors')]
    public function configureFieldClimate(Station $station, Request $request, 
        EntityManagerInterface $em, NumeroCapteurRepository $ncRepo,
        AssocCapteurStationRepository $aCSRepo) : Response
    {
            // extracts from the database the array listing the station sensor codes
        $capteurs = $station->getListeCapteurs();

            // array that will contain the information to display about each sensor
        $form_array=[];
        $numero=0;
        
            // booleans used in the logic
        $allSensorsConfigured=true;
        $currentForm = true;

            // parses the array extracted from the DB containing the sensor codes
        foreach ($capteurs as $capteurName => $capteurCode){
            $numero++;
            $form_array[$capteurCode]['name']=$capteurName;
            $form_array[$capteurCode]['numero']=$numero;
            
                // Checks if the sensor is allready configured
            $existingSetting = $aCSRepo->findOneBy(['codeCapteur'=> $capteurCode,
                                    'station' => $station]);
                // if not yet configured
            if ($existingSetting == null){

                    // registers the configuration as NOT COMPLETED
                $allSensorsConfigured=false;

                $numeroCapteur = $ncRepo->findOneBy(['numero' => $numero]);

                    // if the not configured sensor is the next one to parameter
                if ($currentForm == true){

                        // form to configure the sensor                                    
                    $aCS = new AssocCapteurStation;
                    $aCS->setStation($station);
                    $aCS->setNumeroCapteur($numeroCapteur);
                    $aCS->setCodeCapteur($capteurCode);
                    $form = $this->createFormBuilder($aCS)
                        ->add('capteur', EntityType::class, [
                            'class' => Capteur::class,
                            'query_builder' => function (CapteurRepository $cr) {
                                return $cr->createQueryBuilder('u')
                                    ->orderBy('u.capteurName','ASC');
                            },
                            'choice_label' => 'capteurName',
                            'label' => false,
                            'placeholder' => 'Attribuez le capteur'
                        ])
                        ->getForm();

                    $form->handleRequest($request);

                    if ($form->isSubmitted() && $form->isValid()) {
                        $em->persist($aCS);
                        $em->flush();

                        $this->addFlash('success','Capteur n°'.$numeroCapteur->getNumero().' attribué au code API '.$capteurCode.' : '.$aCS->getCapteur()->getCapteurName().'.');

                        return $this->redirectToRoute('app_stations_addFieldClimate_sensors', ['id' => $station->getId()]);
                    }

                    $form_array[$capteurCode]['form']=$form->createView();


                        // next sensors don't receive the form
                    $currentForm = false;                                                                                                       
                }
                
            }
            else {
                    // if the sensor is allready configured, it is transmitted to the view for display to the user
                $form_array[$capteurCode]['setting']=$existingSetting;
            }
        }
            // if the configuration is done, redirects to the station edit view
        if ($allSensorsConfigured==true){
            $this->addFlash('success', 'Tous les capteurs de la station '.$station->getStationName().' ont été configurés avec succès.');

            return $this->redirectToRoute('app_stations_edit', ['id' => $station->getId()]);
        }

        return $this->render('entities/stations/configureFC.html.twig', [ 'form_array' => $form_array, 'station' => $station ]);

    }




        // route called to DISMISS an IMPORTED SENSOR while AUTOCONFIGURING a FIELDCLIMATE STATION
     
    #[Route('/stationModels/configureFieldClimate/{id<[0-9a-fA-F]{8}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{12}>}/dismiss_sensor/{capteurAPI}', 
            name: 'app_stations_addFieldClimate_dismissSensor')]
    public function fieldClimateDismissSensor(Station $station, $capteurAPI, EntityManagerInterface $em) : Response
    {
            // extracts the sensor array
        $sensors = $station->getListeCapteurs();
            // new sensor array
        $sensorsToReturn = [];
            // build the new array as the old one, minus the sensor to dismiss
        foreach ($sensors as $sensorName => $sensorCode){
            if ($sensorCode != $capteurAPI){
                $sensorsToReturn[$sensorName] = $sensorCode;
            }
        }
            // updates the station with the new array
        $station->setListeCapteurs($sensorsToReturn);
        $em->persist($station);
        $em->flush();
        
        return $this->redirectToRoute('app_stations_addFieldClimate_sensors', ['id' => $station->getId()]);
    }

    


}