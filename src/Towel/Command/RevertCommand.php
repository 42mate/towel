<?php

namespace Towel\Command;

use Towel\Console\BaseCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RevertCommand extends BaseCommand
{
    public function configure()
    {
        $this->setName('migration:revert')
            ->setDescription('Revert a single migration')
            ->addOption(
                'migration',
                null,
                InputOption::VALUE_REQUIRED,
                'Migration to Run.',
                'all'
            );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $migrator = new \Towel\Migration\Migrator();
        $migration = $input->getOption('migration');

        $out = $migrator->revert($migration);

        foreach ($out as $o) {
            echo $o . "\n";
        }
    }
} 