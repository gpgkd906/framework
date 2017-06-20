<?php

namespace Framework\Module\Cngo\Console\Controller;

use Framework\Controller\Controller\AbstractConsole;
use Zend\EventManager\EventManagerAwareInterface;
use Framework\Router\RouterAwareInterface;
use Framework\ObjectManager\ObjectManagerAwareInterface;

class HelpController extends AbstractConsole implements RouterAwareInterface
{
    use \Framework\Router\RouterAwareTrait;
    use \Framework\ObjectManager\ObjectManagerAwareTrait;

    public function index($consoles)
    {
        $routerList = $this->getRouter()->getRouterList();
        $ObjectManager = $this->getObjectManager();
        foreach ($consoles as $console) {
            echo $console, PHP_EOL;
            if (isset($routerList[$console])) {
                $Console = $ObjectManager->create($routerList[$console]);
                $help = $Console->getHelp();
            } else {
                $help = 'invalid Command';
            }
            echo sprintf('%20s', $help), PHP_EOL;
        }
    }

    public function getDescription()
    {
        return 'show help';
    }

    public function getHelp()
    {
        return 'need some help';
    }

    public function getPriority()
    {
        return 1;
    }
}
