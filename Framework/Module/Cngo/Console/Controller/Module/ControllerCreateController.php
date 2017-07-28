<?php

namespace Framework\Module\Cngo\Console\Controller\Module;

use Framework\Controller\AbstractConsole;
use Framework\Controller\AbstractController;
use Zend\EventManager\EventManagerAwareInterface;
use Framework\Module\Cngo\Console\Helper\Console\ConsoleHelperAwareInterface;
use Framework\Module\Cngo\Console\Helper\Generator\GeneratorAwareInterface;

class ControllerCreateController extends AbstractConsole implements GeneratorAwareInterface, ConsoleHelperAwareInterface
{
    use \Framework\Module\Cngo\Console\Helper\Generator\GeneratorAwareTrait;
    use \Framework\Module\Cngo\Console\Helper\Console\ConsoleHelperAwareTrait;

    public function index($args = null)
    {
        if ($args === null) {
            $args = ['test' => 1];
        }
        if (isset($args['test'])) {
            echo PHP_EOL, '---- Test Mode ----', PHP_EOL, PHP_EOL;
            $this->getGenerator()->setTestMode((bool) $args['test']);
        }
        $moduleInfo = $this->getGenerator()->getModuleInfo();
        $moduleName = $this->getConsoleHelper()->ask('Input Module');
        $moduleInfo['module'] = $moduleName;
        $moduleInfo['path'][] = $moduleName;
        $namepace = $this->getConsoleHelper()->ask('Input Namespace');
        $moduleInfo['type'] = $this->getConsoleHelper()->choice('Input Module Type', ['Admin', 'Front', 'Console']);
        $moduleInfo['namespace'] = $namepace;
        $controller = $this->getConsoleHelper()->ask('Input Controller');
        $moduleInfo['controller'] = $controller;
        $moduleInfo['path'] = join('/', $moduleInfo['path']);
        $this->getGenerator()->setModuleInfo($moduleInfo);
        $this->getGenerator()->generateController()->flush();
    }

    public static function getDescription()
    {
        return 'module-controller generator';
    }

    public function getHelp()
    {
        return <<<HELP
Controller Generator

example:
    Input Module? EC\Admin
    Input Namespace? Product
    Input Module Type?[Admin/Front/Console]? Admin
    Input Controller? RegisterController
HELP;
    }
}
