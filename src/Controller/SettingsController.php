<?php

namespace App\Controller;

use App\Entity\Capteur;
use App\Entity\CapteurPourMaths;
use App\Entity\MessageAlerte;
use App\Form\MessageAlerteType;
use App\Repository\CapteurPourMathsRepository;
use App\Repository\CapteurRepository;
use App\Repository\MessageAlerteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SettingsController extends AbstractController
{
    // ROUTE TO CONFIGURE THE SETTINGS OF THE APP
    // 
    #[Route('/settings', name: 'app_settings', methods:'GET|POST')]
    public function appSettings (CapteurRepository $capteurRepo, EntityManagerInterface $em,
        CapteurPourMathsRepository $cpmRepo, Request $request, MessageAlerteRepository $maRepo): Response
    {

        //                ***
        //  PART FOR SETTING THE ALERT LEVELS
        //                ***

        $messageAlerte = new MessageAlerte;
        $alertForm = $this->createForm(MessageAlerteType::class, $messageAlerte);
        $alertForm->handleRequest($request);

        if ($alertForm->isSubmitted() && $alertForm->isValid()){
            $em->persist($messageAlerte);
            $em->flush();

            $this->addFlash('success','Message d\'alerte pour le code '.$messageAlerte->getAlerteCode().' paramétré !');

            return $this->redirectToRoute('app_settings');
        }





        //                          ***
        //  PART TO INDICATE THE SENSORS TO ADD TO THE MATH MODEL
        //                          ***


        $capteursEnDur["tempAir"]["name"] = "Température de l'air";
        $capteursEnDur["humec"]["name"] = "Humectation";
        // $capteursEnDur["test"]["name"] = "Un super test";

        foreach ($capteursEnDur as $shortName => $array) {
                $existingCapteur = $cpmRepo->findOneBy(['nomRaccourci' => $shortName]);
            if ($existingCapteur == null)
            {
                $capteurPourMath = new CapteurPourMaths;
                $capteurPourMath->setNomRaccourci($shortName);
                $form = $this->createFormBuilder($capteurPourMath)
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
                    $em->persist($capteurPourMath);
                    $em->flush();
        
                    $this->addFlash('success','Capteur attribué.');
        
                    return $this->redirectToRoute('app_settings');
                }
                



                $capteursEnDur[$shortName]["form"]=$form->createView();

            }
            else
            {
                $capteursEnDur[$shortName]["capteur"]=$existingCapteur;
                


            }
        }
        // dd($capteursEnDur);



        

        //               ***
        //  PART TO CREATE THE DISPLAY TABLE
        //               ***

        $alerteMessages = $maRepo->findBy([], ['alerteCode' => 'ASC']);

        return $this->render('settings/settings.html.twig', ['form_array' => $capteursEnDur, 'alert_form' => $alertForm->createView(), 'alert_messages' => $alerteMessages]);


    }




    #[Route('/settings/deleteCPM/{id<[0-9a-fA-F]{8}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{12}>}', name: 'app_settings_deleteCPM', methods:'GET|POST')]
    public function appSettingsDeleteCPM (EntityManagerInterface $em, CapteurPourMaths $capteurPourMath): Response
    {
        $em->remove($capteurPourMath);
        $em->flush();

        $this->addFlash('info', 'Configuration supprimée !');

        return $this->redirectToRoute('app_settings');
    }


    #[Route('/settings/deleteAlert/{id<[0-9a-fA-F]{8}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{12}>}', name: 'app_settings_deleteAlert', methods:'GET|POST')]
    public function appSettingsDeleteAlert (EntityManagerInterface $em, MessageAlerte $messageAlerte): Response
    {
        $em->remove($messageAlerte);
        $em->flush();

        $this->addFlash('info', 'Alerte supprimée');

        return $this->redirectToRoute('app_settings');
    }
















}