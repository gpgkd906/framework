<?php
declare(strict_types=1);

namespace Framework\Module\Cngo\Console\Controller\Module;

use Framework\Controller\AbstractConsole;
use Framework\Module\Cngo\Console\Helper\Console\ConsoleHelperAwareInterface;
use Framework\Module\Cngo\Console\Helper\Generator\GeneratorAwareInterface;

class CreateController extends AbstractConsole implements GeneratorAwareInterface, ConsoleHelperAwareInterface
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
        $moduleInfo['path'] = [ROOT_DIR, 'Framework'];
        $moduleName = $this->getConsoleHelper()->ask('Input Module');
        $moduleInfo['module'] = $moduleName;
        $moduleInfo['path'][] = $moduleName;
        $namepace = $this->getConsoleHelper()->ask('Input Namespace');
        $moduleInfo['useAwareInterface'] = $this->getConsoleHelper()->confirm('Use AwareInterface[Y/n]', true);
        $moduleInfo['namespace'] = $namepace;
        $moduleInfo['path'] = join('/', $moduleInfo['path']);
        $this->getGenerator()->setModuleInfo($moduleInfo);
        $this->getGenerator()->generateModule()->flush();
    }

    public static function getDescription()
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
