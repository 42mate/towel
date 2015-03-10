<?php

namespace Towel\Command;

use Towel\Console\BaseCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MigrationStatus extends BaseCommand
{
    public function configure()
    {
        $this->setName('migration:status')
            ->setDescription('Check migration Status');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $migrator = new \Towel\Migration\Migrator();
        $out = $migrator->status();
        foreach ($out as $o) {
            echo $o;
        }
    }
} 