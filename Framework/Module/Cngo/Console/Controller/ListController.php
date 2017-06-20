<?php

namespace Framework\Module\Cngo\Console\Controller;

use Framework\Controller\Controller\AbstractConsole;
use Zend\EventManager\EventManagerAwareInterface;
use Framework\Router\RouterAwareInterface;
use Framework\ObjectManager\ObjectManagerAwareInterface;

class ListController extends AbstractConsole implements RouterAwareInterface
{
    use \Framework\Router\RouterAwareTrait;
    use \Framework\ObjectManager\ObjectManagerAwareTrait;

    public function index()
    {
        $routerList = $this->getRouter()->getRouterList();
        $ObjectManager = $this->getObjectManager();
        $commands = [];
        foreach ($routerList as $cmd => $console) {
            $Console = $ObjectManager->create($console);
            $commands[$cmd] = [
                'cmd' => $cmd,
                'description' => $Console->getDescription(),
                'priority' => $Console->getPriority()
            ];
        }
        //sort commands
        usort($commands, function ($cmd1, $cmd2) {
            return $cmd1['priority'] > $cmd2['priority'] ? 1 : -1;
        });
        //
        foreach ($commands as $command) {
            extract($command);
            echo sprintf('%-60s %s', $cmd, $description), PHP_EOL;
        }
    }

    public function getDescription()
    {
        return 'list all commands';
    }

    public function getHelp()
    {
        return 'need some help';
    }

    public function getPriority()
    {
        return 0;
    }
}
