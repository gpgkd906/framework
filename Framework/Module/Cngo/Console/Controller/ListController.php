<?php
declare(strict_types=1);

namespace Framework\Module\Cngo\Console\Controller;

use Framework\Controller\AbstractConsole;

class ListController extends AbstractConsole
{

    public function index()
    {
        $routerList = $this->getRouter()->getRouterList();
        $ObjectManager = $this->getObjectManager();
        $commands = [];
        foreach ($routerList as $cmd => $console) {
            $Console = $ObjectManager->create($console);
            $commands[$cmd] = [
                'cmd' => $cmd,
                'description' => $console::getDescription(),
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

    public static function getDescription()
    {
        return 'list all commands';
    }

    public function getHelp()
    {
        return <<<HELP
List All Command
Usage:
    php bin/console.php list
HELP;
    }

    public function getPriority()
    {
        return 0;
    }
}
