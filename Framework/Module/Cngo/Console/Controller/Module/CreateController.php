<?php

namespace Framework\Module\Cngo\Console\Controller\Module;

use Framework\Controller\AbstractConsole;
use Framework\Controller\AbstractController;
use Framework\ViewModel\ViewModel\AbstractViewModel;
use Zend\EventManager\EventManagerAwareInterface;
use Framework\Service\CodeService\CodeServiceAwareInterface;
use Framework\Module\Cngo\Console\Helper\Console\ConsoleHelperAwareInterface;

class CreateController extends AbstractConsole implements CodeServiceAwareInterface, ConsoleHelperAwareInterface
{
    use \Framework\Service\CodeService\CodeServiceAwareTrait;
    use \Framework\Module\Cngo\Console\Helper\Console\ConsoleHelperAwareTrait;

    const ROUTER_ADMIN = 'Admin';
    const ROUTER_FRONT = 'Front';
    const ROUTER_CONSOLE = 'Console';

    public function index()
    {
        //list($moduleName, $moduleType) = $this->getDummy();
        $moduleInfo = [
            'path' => [
                ROOT_DIR, 'Framework', 'Module',
            ],
            'namespace' => [
                \Framework\Module::class
            ],
            'type' => null,
            'router' => [],
        ];
        $moduleName = $this->getConsoleHelper()->ask('Input Module Name');
        $moduleInfo['path'][] = $moduleName;
        $moduleInfo['namespace'][] = $moduleName;
        $moduleType = $this->getConsoleHelper()->choice('Input Module Type', ['Admin', 'Front', 'Console']);
        $moduleInfo['type'] = $moduleType;
        $router = $this->getConsoleHelper()->ask('Input Router', '');
        if ($router) {
            $controller = $this->getConsoleHelper()->ask("Input Controller Name which match the router[$router]");
            $moduleInfo['router'][] = [
                'router' => $router,
                'controller' => $controller
            ];
            while ($this->getConsoleHelper()->confirm('Add Other router[y/n]', false, ['y', 'Y', 'yes', 'Yes'])) {
                $router = $this->getConsoleHelper()->ask('Input Router', '');
                if (!$router) break;
                $controller = $this->getConsoleHelper()->ask("Input Controller Name which match the router[$router]");
                $moduleInfo['router'][] = [
                    'router' => $router,
                    'controller' => $controller
                ];
            }
        }
        $this->generateModule($moduleInfo);
    }

    public function generateModule($moduleInfo)
    {
        $path = join('/', $moduleInfo['path']);
        $moduleInfo['path'] = $path;
        $namespace = join('\\', $moduleInfo['namespace']);
        $moduleInfo['namespace'] = $namespace;
        if (is_dir($path)) {

        }
        // make router
        $this->generateRoute($moduleInfo);
        // make controller
        if ($moduleInfo['type'] === self::ROUTER_CONSOLE) {
            $this->generateConsole($moduleInfo);
        } else {
            $this->generateController($moduleInfo);
        }
    }

    private function generateRoute($moduleInfo)
    {
        $namespace = $moduleInfo['namespace'];
        $path = $moduleInfo['path'];
        if ($moduleInfo['type'] === self::ROUTER_CONSOLE) {
            $routerInjector = $path . '/Command.php';
        } else {
            $routerInjector = $path . '/Route.php';
        }
        $routerCode = [
            '<?php', PHP_EOL,
            'namespace ', $namespace, ';', PHP_EOL, PHP_EOL,
            'use Framework\Router\RouterInterface;', PHP_EOL,
            'use Framework\ObjectManager\ObjectManager;', PHP_EOL, PHP_EOL,
            'ObjectManager::getSingleton()->get(RouterInterface::class)', PHP_EOL,
            '    ->register([', PHP_EOL,
        ];
        foreach ($moduleInfo['router'] as $route) {
            $router = $route['router'];
            $controller = $route['controller'];
            $routerCode[] = "'$router' => Controller\\$controller::class,";
            $routerCode[] = PHP_EOL;
        }
        $routerCode[] = '    ]);';
        $routerCode = join('', $routerCode);
        $routerCode = $this->getCodeService()->analysisCode($routerCode);
        $this->write($routerInjector, $routerCode);
    }

    private function generateConsole($moduleInfo)
    {
        $CodeService = $this->getCodeService();
        $path = $moduleInfo['path'];
        $namespace = $moduleInfo['namespace'];
        foreach ($moduleInfo['router'] as $route) {
            $controller = $route['controller'];
            $controllerPath = $path . '/Controller/' . $controller . '.php';
            $Code = $CodeService->createCode($namespace . '\Controller', $controller);
            $Code->getNamespace()->appendUse(AbstractConsole::class);
            $Code->getClass()->extend('AbstractConsole');
            $Code->getClass()->appendMethod('index');
            $this->write($controllerPath, $Code);
        }
    }

    private function generateController($moduleInfo)
    {
        $CodeService = $this->getCodeService();
        $path = $moduleInfo['path'];
        $namespace = $moduleInfo['namespace'];
        foreach ($moduleInfo['router'] as $route) {
            $controller = $route['controller'];
            $viewModel = str_replace('Controller', 'ViewModel', $controller);
            $viewNamespace = $namespace . '\View\ViewModel';
            $controllerPath = $path . '/Controller/' . $controller . '.php';
            $Code = $CodeService->createCode($namespace . '\Controller', $controller);
            $Code->getNamespace()->appendUse(AbstractController::class);
            $Code->getNamespace()->appendUse($viewNamespace . '\\' . $viewModel);
            $Code->getClass()->extend('AbstractController');
            $Code->getClass()->appendMethod('index');
            $Code->getClass()->getMethod('index')->setReturn("ViewModelManager::getViewModel(['viewModel' => $viewModel::class])");
            $this->generateViewModel($controller, $moduleInfo);
            $this->write($controllerPath, $Code);
        }
    }

    private function generateViewModel($controller, $moduleInfo)
    {
        $path = $moduleInfo['path'];
        $namespace = $moduleInfo['namespace'];
        $viewModel = str_replace('Controller', 'ViewModel', $controller);
        $template = strtolower(str_replace('Controller', '', $controller));
        $viewNamespace = $namespace . '\View\ViewModel';
        $viewModelPath = $path . '/View/ViewModel/' . $viewModel . '.php';
        $templatePath = $path . '/View/template/' . $template . '.phtml';
        $CodeService = $this->getCodeService();
        $ViewModelCode = $CodeService->createCode($viewNamespace, $viewModel);
        $ViewModelCode->getNamespace()->appendUse(AbstractViewModel::class);
        $ViewModelCode->getClass()->extend('AbstractViewModel');
        $ViewModelCode->getClass()->appendProperty('template', '/template/' . $template . '.phtml');
        $ViewModelCode->getClass()->appendMethod('getTemplateDir');
        $ViewModelCode->getClass()->getMethod('getTemplateDir')->setReturn("__DIR__ . '/..'");
        var_dump($ViewModelCode->toCode());

    }

    public function write($file, $Code)
    {

    }

    public static function getDescription()
    {
        return 'module generator';
    }

    public function getHelp()
    {
        return <<<HELP
Module Generator
    module generator configuration
HELP;
    }
}
