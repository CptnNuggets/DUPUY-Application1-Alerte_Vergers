<?php

namespace App\Service;

use App\Repository\MessageAlerteRepository;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

    // Service used to SEND EMAILS

class MailerAlert
{
        // Mailer Interface 
    private $mailer;
        // Data Manipulation Service
    private $dataManip;
        // Repository for the alert Levels
    private $alertRepo;
    
    public function __construct(MailerInterface $mailer, DataManipulation $dataManip, MessageAlerteRepository $alertRepo)
    {
        $this->mailer = $mailer;
        $this->dataManip = $dataManip;
        $this->alertRepo = $alertRepo;
    }


        // calls the DATAMANIPULATION necessary functions then SEND the necessary EMAILS

    public function sendAlertEmails(){
 
            // Returns an array of [ verger_id => vergerName, contact, riskCode]
        $vergersAndRisks = $this->dataManip->determineActiveAlertLevels();
        
            // returns an ARRAY containing [ alertCode => alertMessage ]
        $messagesArray = $this->getAlertMessages();

        foreach ($vergersAndRisks as $vergerId => $array){
            $messageContent = $array["vergerName"] . " : " . $messagesArray[$array["riskCode"]];
            $messageSubject = "Alerte au verger : " . $array["vergerName"];
            $this->sendEmail($array["contact"], $messageSubject, $messageContent);
        }
    }



        //  SENDS an EMAIL containing the alert

    public function sendEmail($address, $subject, $content) : string
    {
        $email = (new Email())
            ->from('alerte.vergers@vasnanciaco.com')
            ->to($address)
            ->subject($subject)
            ->text($content)
            ->html($content);

        try {
            $this->mailer->send($email);
            return ('success !');
        } 
        catch (TransportExceptionInterface $e) { 
            return $e;
        }
    }


        // returns an ARRAY containing [ alertCode => alertMessage ]

    public function getAlertMessages(){

        $allMessageAlerts = $this->alertRepo->findAll();

        $messagesArray =[];

        foreach ($allMessageAlerts as $messageAlert){
            $code = $messageAlert->getAlerteCode();
            $message = $messageAlert->getAlerteMessage();
            $messagesArray[$code] = $message;
        }
        return $messagesArray;
    }

    
}