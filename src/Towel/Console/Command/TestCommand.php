<?php
namespace Towel\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Towel\Console\BaseCommand;

class TestCommand extends BaseCommand
{
    public function configure() {
        $this->setName('test:command')
            ->setDescription('some Description');

    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        echo 'Abemus Console';
    }
}

