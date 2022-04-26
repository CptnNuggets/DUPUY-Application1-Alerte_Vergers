<?php

namespace App\Command;

use App\Service\FieldClimateRequests;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;


#[AsCommand(
    name: 'app:first-try',
    description: 'Just to see how it works !',
)]
class FirstTryCommand extends Command
{
    private $fCRequests;

    public function __construct(FieldClimateRequests $fCRequests)
    {
        $this->fCRequests = $fCRequests;

        parent::__construct();        
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln([
            'Super Test',
            '============',
            '',
        ]);

        $success = $this->fCRequests->autoStoreMissingData();

        if ($success == true) {
            $output->writeln([
                '============',
                'This is the way !',
                '',
            ]);
            sleep(10);
            return Command::SUCCESS;
        }
                
    }
}
