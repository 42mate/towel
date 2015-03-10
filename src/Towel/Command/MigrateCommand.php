<?php

namespace Towel\Command;

use Towel\Console\BaseCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateCommand extends BaseCommand
{
    public function configure()
    {
        $this->setName('migration:migrate')
            ->setDescription('Run all pending migrations')
            ->addOption(
                'migration',
                null,
                InputOption::VALUE_OPTIONAL,
                'Migration to Run.',
                'all'
            );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $migrator = new \Towel\Migration\Migrator();
        $migration = $input->getOption('migration');

        if ($migration == 'all') {
            $migration = null;
        }

        $out = $migrator->migrate($migration);

        foreach ($out as $o) {
            echo $o;
        }
    }
} 