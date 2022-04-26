<?php

namespace App\Controller;

use App\Entity\AssocCapteurStation;
use App\Entity\Station;
use App\Form\ChooseStationType;
use App\Repository\AssocCapteurStationRepository;
use App\Repository\MesureRepository;
use App\Repository\StationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MesureController extends AbstractController
{
    // INDEX ROUTE OFFERING THE POSSIBILITY TO CHOOSE A STATION AND DISPLAY ITS MESURES
    // 
    #[Route('/mesures', name: 'app_mesures_home')]
    public function selectStationForMesures(Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ChooseStationType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $station = $form->getData()['station'];
            
            return $this->redirectToRoute('app_mesures_station', ['id' => $station->getId()]);
        }
        return $this->renderForm('entities/mesures/index.html.twig', compact('form'));
    }




    // ROUTE TO DISPLAY THE MESURES FOR A GIVEN STATION 
    // RENDER AN ARRAY WITH MESURES ORGANISED BY SENSOR AND TIME/DATE
    // 
    #[Route('/mesures/station/{id<[0-9a-fA-F]{8}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{12}>}', 
        name: 'app_mesures_station', methods:'GET|POST')]

    public function listMesuresByStation (MesureRepository $mesRepo, AssocCapteurStationRepository $aCSRepo,
        Request $request, EntityManagerInterface $em, Station $station): Response
    {
        $form = $this->createForm(ChooseStationType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $stationToRedirect = $form->getData()['station'];
            
            return $this->redirectToRoute('app_mesures_station', ['id' => $stationToRedirect->getId()]);
        }
                
        // $mesures = $mesRepo->findBy(['station' => $station],['dateTime' => 'DESC']);

        // $associations = $aCSRepo ->findBy(['station' => $station]);
        // $listCapteurs=[];
        // foreach ($associations as $asso){
        //     $capteur = $asso->getCapteur();
        //     $numero = $asso->getNumeroCapteur()->getNumero();
        //     $listCapteurs[$numero]=$capteur;
        // }
        // ksort($listCapteurs);

        // $dataSortedByDate = [];
        // foreach ($mesures as $mesure){
        //     $dateTimeAsString = $mesure->getDateTime()->format('Y-m-d H:i:s');
        //     $dataSortedByDate[$dateTimeAsString][$mesure->getAssoCapteurStation()->getNumeroCapteur()->getNumero()]=$mesure->getValeur();
        // }

        

        $associations = $aCSRepo ->findBy(['station' => $station]);

        
        $listCapteurs=[];
        $dataSortedByDate = [];
        foreach ($associations as $asso){

            $capteur = $asso->getCapteur();
            $numero = $asso->getNumeroCapteur()->getNumero();
            $listCapteurs[$numero]=$capteur;

            $mesures = $mesRepo->findBy(['assocCapteurStation' => $asso],['dateTime' => 'DESC']);
            
            // dd($mesures);
            foreach ($mesures as $mesure){
                $dateTimeAsString = $mesure->getDateTime()->format('Y-m-d H:i:s');
                $dataSortedByDate[$dateTimeAsString][$numero]=$mesure->getValeur();
            }
        }
        ksort($listCapteurs);     


        return $this->renderForm('entities/mesures/index.html.twig', compact('form', 'listCapteurs', 'dataSortedByDate', 'station'));
    }



    // ROUTE TO DELETE A LINE OF MESURES FROM A STATION GIVEN A DATETIME
    // 
    #[Route('/mesures/delete/{stationId<[0-9a-fA-F]{8}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{12}>}/{dateTime<[0-9]{4}\-[0-9]{2}\-[0-9]{2}\ [0-9]{2}\:[0-9]{2}\:[0-9]{2}>}', name: 'app_mesures_delete')]
    public function deleteMesures($stationId, $dateTime, EntityManagerInterface $em, 
        MesureRepository $mesureRepo, StationRepository $stationRepo, AssocCapteurStationRepository $aCSRepo)
    {
        $station = $stationRepo->findOneBy(['id' => $stationId]);
        $associations = $aCSRepo->findBy(['station' => $station]);
        $dateTimeFormatted = \DateTime::createFromFormat("Y-m-d H:i:s", $dateTime);
        foreach ($associations as $asso){
            $mesuresToDelete = $mesureRepo->findBy(['assocCapteurStation' => $asso, 'dateTime' => $dateTimeFormatted]);
            foreach ($mesuresToDelete as $mesure) {
                $em->remove($mesure);
            }
        }        
        $em->flush();
        $this->addFlash('info', 'Mesures du '.$dateTime.' supprimées.');

        return $this->redirectToRoute('app_mesures_station', ['id' => $stationId]);
    }
}
