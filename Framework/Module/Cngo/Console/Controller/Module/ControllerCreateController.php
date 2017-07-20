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

    public function index($args)
    {
        if (isset($args['test'])) {
            $this->getGenerator()->setTestMode((bool) $args['test']);
        }
        $moduleInfo = $this->getGenerator()->getModuleInfo();
        $moduleName = $this->getConsoleHelper()->ask('Input Module');
        $moduleInfo['path'][] = $moduleName;
        $moduleInfo['namespace'][] = $moduleName;
        $namepace = $this->getConsoleHelper()->ask('Input Namespace');
        $moduleInfo['path'][] = 'Controller';
        $moduleInfo['path'][] = $namepace;
        $moduleInfo['namespace'][] = 'Controller';
        $moduleInfo['namespace'][] = $namepace;
        $controller = $this->getConsoleHelper()->ask("Input Controller Name");
        $moduleInfo['router'][] = [
            'controller' => $controller
        ];
        while ($this->getConsoleHelper()->confirm('Add Other Controller[y/n]', false, ['y', 'Y', 'yes', 'Yes'])) {
            $router = $this->getConsoleHelper()->ask('Input Router', '');
            if (!$router) break;
            $controller = $this->getConsoleHelper()->ask("Input Controller Name which match the router[$router]");
            $moduleInfo['router'][] = [
                'controller' => $controller
            ];
        }
        $moduleInfo['path'] = join('/', $moduleInfo['path']);
        $moduleInfo['namespace'] = join('\\', $moduleInfo['namespace']);
        $this->getGenerator()->setModuleInfo($moduleInfo);
        $this->getGenerator()->generateController();
    }

    public static function getDescription()
    {
        return 'module-controller generator';
    }

    public function getHelp()
    {
        return <<<HELP
Module Generator
    module generator configuration
HELP;
    }
}
