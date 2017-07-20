<?php

namespace Framework\Module\Cngo\Console\Controller;

use Framework\Controller\AbstractConsole;
use Zend\EventManager\EventManagerAwareInterface;
use Framework\Router\RouterAwareInterface;
use Framework\ObjectManager\ObjectManagerAwareInterface;

class HelpController extends AbstractConsole implements RouterAwareInterface
{
    use \Framework\Router\RouterAwareTrait;
    use \Framework\ObjectManager\ObjectManagerAwareTrait;

    public function index($consoles)
    {
        if (empty($consoles)) {
            $consoles = ['help'];
        }
        $consoles = array_unique($consoles);
        $routerList = $this->getRouter()->getRouterList();
        $ObjectManager = $this->getObjectManager();
        foreach ($consoles as $index => $console) {
            if ($index) {
                echo '---------------------------------------------------------', PHP_EOL;
            }
            if (isset($routerList[$console])) {
                $Console = $ObjectManager->create($routerList[$console]);
                $help = $Console->getHelp();
            } else {
                $help = $console . PHP_EOL;
                $help .= sprintf('%20s', 'invalid Command');
            }
            echo $help, PHP_EOL, PHP_EOL;
        }
    }

    public static function getDescription()
    {
        return 'see help for command';
    }

    public function getHelp()
    {
        return <<<HELP
Help
Usage:
    php bin/console.php <command> [<args>...]

Some commands
    list                            List All Command
    help                            See Help for Command
    cngo::module::create            module generator

See 'php bin/console.php help <command>' for more information on a specific command.
HELP;
    }

    public function getPriority()
    {
        return 1;
    }
}
