<?php

namespace App\Controller;

use App\Entity\AssocStationVerger;
use App\Form\AssocStationVergerType;
use App\Repository\AssocStationVergerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AssocStationVergerController extends AbstractController
{

    // route RENAMED as APP_ASSOCSV_LIST in the CLIENT VERSION
    // 
        // INDEX ROUTE FOR STATION-VERGER ASSOCIATIONS DISPLAY
            // Displays a list of existing associations and a form to create a new one

    #[Route('/', name: 'app_home', methods:'GET|POST')]
    public function index(AssocStationVergerRepository $assocStaVerRepo,
            Request $request, EntityManagerInterface $em): Response
    {
        $associations = $assocStaVerRepo->findBy([],['station' => 'ASC']);

        $assoc = new AssocStationVerger;
        $form = $this->createForm(AssocStationVergerType::class, $assoc);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($assoc);
            $em->flush();

            $this->addFlash('success', $assoc->getVerger()->getIdVerger().' associé à la station '.$assoc->getStation()->getStationName().' !');

            return $this->redirectToRoute('app_home');

        }

        return $this->renderForm('entities/assocsStationVerger/index.html.twig', compact('associations' , 'form'));
    }




        // route called to DELETE a STATION-VERGER ASSOCIATION
     
    #[Route('/assocSV/delete/{id<[0-9a-fA-F]{8}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{12}>}', 
        name: 'app_assocSV_delete', methods:'GET|POST')]
    public function deleteAssocSV(EntityManagerInterface $em, AssocStationVerger $association)
    {
        $em->remove($association);
        $em->flush();

        $this->addFlash('info', 'Association de '.$association->getVerger()->getIdVerger().' à la station '.$association->getStation()->getStationName().' supprimé avec succès !');

        return $this->redirectToRoute('app_home');
    }
}