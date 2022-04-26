<?php

namespace App\Controller;

use App\Entity\Verger;
use App\Form\VergerType;
use App\Repository\VergerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VergerController extends AbstractController
{
                // INDEX ROUTE FOR THE VERGERS
            //  
    #[Route('/vergers', name: 'app_vergers_list', methods:'GET|POST')]
    public function listVergers(VergerRepository $vergerRepository,
            Request $request, EntityManagerInterface $em): Response
    {
        $vergers = $vergerRepository->findBy([],['id' => 'ASC']);

        $verger = new Verger;
        $form = $this->createForm(VergerType::class, $verger);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($verger);
            $em->flush();

            $this->addFlash('success','Verger créé !');

            return $this->redirectToRoute('app_vergers_list');

        }

        return $this->render('entities/vergers/index.html.twig', [ 'vergers' => $vergers , 'form' => $form->createView()]);
    }

            // FORM TO MODIFY VERGERS
            // 
    #[Route('/vergers/edit/{id<[0-9a-fA-F]{8}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{12}>}', 
        name: 'app_vergers_edit', methods:'GET|POST')]
    public function editVerger(EntityManagerInterface $em, Verger $verger, Request $request)
    {
        $form = $this->createForm(VergerType::class, $verger);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $em->flush();

            $this->addFlash('success','Verger modifié correctement !');

            return $this->redirectToRoute('app_vergers_list');
        }

        return $this->render('entities\vergers\edit.html.twig', ['verger'=>$verger, 'form'=>$form->createView()]);
    }

    // TO ULTIMATELY DELETE : 
            // ROUTE TO DELETE A VERGER
            // 
    #[Route('/vergers/delete/{id<[0-9a-fA-F]{8}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{12}>}', 
        name: 'app_vergers_delete', methods:'GET|POST')]
    public function deleteVeger(EntityManagerInterface $em, Verger $verger)
    {
        $em->remove($verger);
        $em->flush();

        $this->addFlash('info', 'Le verger a été supprimé !');

        return $this->redirectToRoute('app_vergers_list');
    }
}