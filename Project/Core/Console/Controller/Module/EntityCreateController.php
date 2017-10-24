<?php
declare(strict_types=1);

namespace Project\Core\Console\Controller\Module;

use Std\Controller\AbstractConsole;
use Std\Controller\AbstractController;
use Project\Core\Console\Helper\Console\ConsoleHelperAwareInterface;
use Project\Core\Console\Helper\Generator\GeneratorAwareInterface;

class EntityCreateController extends AbstractConsole implements GeneratorAwareInterface, ConsoleHelperAwareInterface
{
    use \Project\Core\Console\Helper\Generator\GeneratorAwareTrait;
    use \Project\Core\Console\Helper\Console\ConsoleHelperAwareTrait;

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
