<?php

/**
 * @todo create some tools for the console. in order to be executed inside of the
 * application dir to read the configuration of the app and apply tasks.
 */

$app = require_once __DIR__ . '/../bootstrap.php';

//Include the namespaces of the components we plan to use
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

//Instantiate our Console application
$console = new Application('FrameworkTasks', '0.1');

//Register a command to run from the command line
//Our command will be started with "./console.php sync"
$console->register('action')
    ->setDefinition(array())
    ->setDescription('Some Action')
    ->setHelp('Usage: <info>./php app/console/action.php</info>')
    ->setCode(
        function (InputInterface $input, OutputInterface $output) use ($app) {
            $output->write("Do Something");
        }
    );

$console->run();