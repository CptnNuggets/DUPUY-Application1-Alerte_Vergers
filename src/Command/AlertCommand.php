<?php

namespace App\Command;

use App\Service\MailerAlert;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:alert-mailer',
    description: 'Identifies alerts to send at the current hour and sends them',
)]
class AlertCommand extends Command
{
    private $mailerAlert;

    public function __construct(MailerAlert $mailerAlert )
    {
        $this->mailerAlert = $mailerAlert;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Compiling alerts and sending',
            '============================',
        ]);

        $this->mailerAlert->sendAlertEmails();

        $output->writeln([
            '* * * * * * * *',
            'Command done !',
        ]);
        sleep(5);
    }
}