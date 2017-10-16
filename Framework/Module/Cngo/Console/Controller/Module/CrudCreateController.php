<?php
declare(strict_types=1);

namespace Framework\Module\Cngo\Console\Controller\Module;

use Framework\Controller\AbstractConsole;
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
        $moduleInfo['module'] = $moduleName;
        $moduleInfo['path'][] = $moduleName;
        $namepace = $this->getConsoleHelper()->ask('Input Namespace');
        $moduleInfo['type'] = $this->getConsoleHelper()->choice('Input Module Type', ['Admin', 'Front', 'Console']);
        $moduleInfo['namespace'] = $namepace;
        $EntityName = $this->getConsoleHelper()->ask("Input Entity Name");
        $moduleInfo['entity'] = $EntityName;
        $moduleInfo['path'] = join('/', $moduleInfo['path']);
        $this->getGenerator()->setModuleInfo($moduleInfo);
        $this->getGenerator()->generateCrud()->flush();
    }

    public function getDescription()
    {
        return 'module crud generator';
    }

    public function getHelp()
    {
        return <<<HELP
Crud Generator

example:
    Input Module? Customer\Admin
    Input Namespace? Customer
    Input Module Type?[Admin/Front/Console]? Admin
    Input Entity Name? Customer
HELP;
    }
}
