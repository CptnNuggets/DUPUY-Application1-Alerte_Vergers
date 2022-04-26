<?php

namespace App\Command;

use App\Service\FieldClimateRequests;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


#[AsCommand(
    name: 'app:store-data',
    description: 'Stores the missing data for each station configured in the database',
)]
class StoreDataCommand extends Command
{
    private $fCRequests;

    public function __construct(FieldClimateRequests $fCRequests)
    {
        $this->fCRequests = $fCRequests;

        parent::__construct();        
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Compiling data and storing',
            '============================',
        ]);

        $this->fCRequests->autoStoreMissingData();

        $output->writeln([
            '* * * * * * * *',
            'Command done !',
        ]);
        sleep(5);
                
    }
}
