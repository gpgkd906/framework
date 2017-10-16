<?php
declare(strict_types=1);

namespace Framework\Module\Cngo\Console\Controller\Module;

use Framework\Controller\AbstractConsole;
use Framework\Controller\AbstractController;
use Framework\Module\Cngo\Console\Helper\Console\ConsoleHelperAwareInterface;
use Framework\Module\Cngo\Console\Helper\Generator\GeneratorAwareInterface;

class EntityCreateController extends AbstractConsole implements GeneratorAwareInterface, ConsoleHelperAwareInterface
{
    use \Framework\Module\Cngo\Console\Helper\Generator\GeneratorAwareTrait;
    use \Framework\Module\Cngo\Console\Helper\Console\ConsoleHelperAwareTrait;

    public function index($args = null)
    {
        $moduleInfo = $this->getGenerator()->getModuleInfo();
        $moduleName = $this->getConsoleHelper()->ask('Input Module');
        $moduleInfo['module'] = $moduleName;
        $moduleInfo['namespace'] = \Framework\Module::class . '\\' . $moduleName;
        $moduleInfo['path'][] = $moduleName;
        $EntityName = $this->getConsoleHelper()->ask("Input Table Name");
        $moduleInfo['table'] = $EntityName;
        $moduleInfo['path'] = join('/', $moduleInfo['path']);
        $this->getGenerator()->setModuleInfo($moduleInfo);
        $this->getGenerator()->generateEntity()->flush();
    }

    public function getDescription()
    {
        return 'module entity generator';
    }

    public function getHelp()
    {
        return <<<HELP
Entity Generator

example:
    Input Module? Cms\Admin
    Input Table Name? blogs
HELP;
    }
}
