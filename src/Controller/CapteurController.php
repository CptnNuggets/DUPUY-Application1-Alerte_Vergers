<?php

namespace App\Controller;

use App\Entity\Capteur;
use App\Form\CapteurType;
use App\Repository\CapteurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CapteurController extends AbstractController
{
    // INDEX ROUTE FOR THE SENSORS, GIVING A LIST OF ALL SENSORS EXISTING IN THE DB
    // AND PROVIDING A FORM TO CREATE A NEW ONE
    // 
    #[Route('/capteurs', name: 'app_capteurs_list', methods:'GET|POST')]
    public function listCapteurs(CapteurRepository $capteurRepository,
            Request $request, EntityManagerInterface $em): Response
    {
        $capteurs = $capteurRepository->findBy([],['id' => 'ASC']);

        $capteur = new Capteur;
        $form = $this->createForm(CapteurType::class, $capteur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($capteur);
            $em->flush();

            $this->addFlash('success','Capteur '.$capteur->getCapteurName().' créé avec succès avec l\'unité '.$capteur->getUnite().' !');

            return $this->redirectToRoute('app_capteurs_list');

        }

        return $this->renderForm('entities/capteurs/index.html.twig', compact('capteurs' , 'form' ));
    }

    
    
    
    // ROUTE TO MODIFY A SENSOR
    // 
    #[Route('/capteurs/edit/{id<[0-9a-fA-F]{8}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{12}>}', 
        name: 'app_capteurs_edit', methods:'GET|POST')]
    public function editCapteur(EntityManagerInterface $em, Capteur $capteur, Request $request)
    {
        $form = $this->createForm(CapteurType::class, $capteur);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $em->flush();

            $this->addFlash('success', 'Le capteur a été modifié !');

            return $this->redirectToRoute('app_capteurs_list');
        }

        return $this->renderForm('entities\capteurs\edit.html.twig', compact('capteur', 'form'));
    }



    // ROUTE DE DELETE A SENSOR
    // 
    #[Route('/capteurs/delete/{id<[0-9a-fA-F]{8}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{12}>}', 
        name: 'app_capteurs_delete', methods:'GET|POST')]
    public function deleteCapteur(EntityManagerInterface $em, Capteur $capteur)
    {
        $em->remove($capteur);
        $em->flush();

        $this->addFlash('info', 'Capteur supprimé !');

        return $this->redirectToRoute('app_capteurs_list');
    }


}