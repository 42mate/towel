<?php

namespace Towel\Command;

use Towel\Console\BaseCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ServeCommand extends BaseCommand
{
    public function configure()
    {
        $this->setName('serve')
            ->setDescription('Serve the application on the PHP development server')
            ->addOption(
                'host',
                null,
                InputOption::VALUE_OPTIONAL,
                'The host address to serve the application on.',
                'localhost'
            )
            ->addOption(
                'port',
                null,
                InputOption::VALUE_OPTIONAL,
                'The port to serve the application on.',
                8000
            );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        chdir(APP_ROOT_DIR);
        $host = $input->getOption('host');
        $port = $input->getOption('port');
        $output->writeln("<info>Towel development server started on http://{$host}:{$port}</info>");
        passthru('"'.PHP_BINARY.'"'." -S {$host}:{$port} -t \"". APP_WEB_DIR ."\"");
    }
} 