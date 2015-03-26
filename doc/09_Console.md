# Console

Towel have support for console commands via the Symfony Console component and the KnpLabs service provider.

## Run commands

In the root of the project use

```bash
php towel.php command [paramters]
```

## Environment executions

In the web execution Towel uses the $_SERVER http_host entry to detect where is running, using that info will try to look for override config files based in the host_name, for example, if we have local.server.com for local work, dev.server.com for dev and www.server.com for production; we can have a default config.php with the default configuration values for local. In dev we can add another file called dev.server.com.config.php, this file will be included after config.php and here we can override the default config with values for the dev environment.
We can do the same in prod using www.server.com.config.php, if we have multiple server names you can create symlinks to the same file.

The problem is that in the php cli there is no HTTP_HOST variable, so in order to let the console know which server is you must add --env=server.com, for example --env=www.server.com, by default will use the config.php file.

## Implement new Commands

To implement command create a new class that inheriths use Towel\Console\BaseCommand and save it into your application Command folder, towel will lookup for the command automatically and will let you available via php towel.php.

The class must be like this.

```php
<?php

namespace Application\YourAppName\Command;

use Towel\Console\BaseCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SampleCommand extends BaseCommand {

  public function configure() {
    $this->setName('sample')
      ->setDescription('Sample Command');
  }


  public function execute(InputInterface $input, OutputInterface $output)
  {
    //do something here !
  }
}
```

You must include env option if you'll need to access to the configuration in your command.

```php
public function configure() {
    $this->setName('sample')
      ->setDescription('This is a sample')
      ->addOption('env', null, InputOption::VALUE_OPTIONAL, 'Environment');
  }
```  
