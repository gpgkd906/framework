<?php

namespace Framework\Module\Cngo\Console\Controller\Module;

use Framework\Controller\AbstractConsole;
use Framework\Controller\AbstractController;
use Zend\EventManager\EventManagerAwareInterface;
use Framework\Module\Cngo\Console\Helper\Console\ConsoleHelperAwareInterface;
use Framework\Module\Cngo\Console\Helper\Generator\GeneratorAwareInterface;

class CrudCreateController extends AbstractConsole implements GeneratorAwareInterface, ConsoleHelperAwareInterface
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
        $moduleInfo['path'][] = $moduleName;
        $moduleInfo['namespace'][] = $moduleName;
        $namepace = $this->getConsoleHelper()->ask('Input Namespace');
        $moduleInfo['path'][] = 'Controller';
        $moduleInfo['path'][] = $namepace;
        $moduleInfo['namespace'][] = 'Controller';
        $moduleInfo['namespace'][] = $namepace;
        $EntityName = $this->getConsoleHelper()->ask("Input Entity Name");
        $moduleInfo['crud'] = [
            'Entity' => $EntityName
        ];
        $moduleInfo['path'] = join('/', $moduleInfo['path']);
        $moduleInfo['namespace'] = join('\\', $moduleInfo['namespace']);
        $this->getGenerator()->setModuleInfo($moduleInfo);
        $this->getGenerator()->generateCrud()->flush();
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
