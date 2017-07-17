<?php

namespace Framework\Module\Cngo\Console\Controller;

use Framework\Controller\AbstractConsole;
use Framework\Repository\EntityManager;
use Doctrine\ORM\Tools\Console\ConsoleRunner;

class DoctrineController extends AbstractConsole
{
    public function index()
    {
        // cut off the noisy argument
        unset($_SERVER['argv'][1]);
        $_SERVER['argv'] = array_values($_SERVER['argv']);
        $EntityManager = $this->getObjectManager()->get(EntityManager::class);
        ConsoleRunner::run(ConsoleRunner::createHelperSet($EntityManager));
    }

    public function getDescription()
    {
        return 'doctrine-command Alias';
    }

    public function getHelp()
    {
        return <<<HELP
Help
Usage:
    php bin/console.php doctrine [<args>...]
HELP;
    }
}
