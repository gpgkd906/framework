<?php

namespace Framework\Module\Cngo\Console\Controller\Module;

use Framework\Controller\Controller\AbstractConsole;
use Zend\EventManager\EventManagerAwareInterface;
use Framework\Service\CodeService\CodeServiceAwareInterface;

class CreateController extends AbstractConsole implements CodeServiceAwareInterface
{
    use \Framework\Service\CodeService\CodeServiceAwareTrait;

    CONST ROUTER_ADMIN = 'Admin';
    CONST ROUTER_FRONT = 'Front';
    CONST ROUTER_CONSOLE = 'Console';

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
        while(!$moduleName = self::readline('Input Module Name'));
        $moduleInfo['path'][] = $moduleName;
        $moduleInfo['namespace'][] = $moduleName;
        while(!$moduleType = self::readline('Input Module Type[Admin/Front/Console]'));
        $moduleInfo['type'] = $moduleType;
        $router = self::readline('Input Router');
        if ($router) {
            while(!$controller = self::readline("Input Controller Name which match the router[$router]"));
            $moduleInfo['router'][] = [
                'router' => $router,
                'controller' => $controller
            ];
            while(in_array(self::readline('Add Other router[y/N]'), ['y', 'Y', 'yes', 'Yes'])) {
                $router = self::readline('Input Router');
                if (!$router) break;
                while(!$controller = self::readline("Input Controller Name which match the router[$router]"));
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
        $namespace = join('\\', $moduleInfo['namespace']);
        if (!is_dir($path)) {
            mkdir($path);
        }
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
        foreach($moduleInfo['router'] as $route) {
            $router = $route['router'];
            $controller = $route['controller'];
            $routerCode[] = "'$router' => Controller\\$controller::class,";
            $routerCode[] = PHP_EOL;
        }
        $routerCode[] = '    ]);';
        $routerCode = join('', $routerCode);
        $routerCode = $this->getCodeService()->analysisCode($routerCode);
        var_dump(
            $routerCode->toCode()
        );
        die;
        // make router
        $this->generateRoute($moduleInfo);
        // make controller
        $CodeService = $this->getCodeService();
        $Code = $CodeService->createCode(__NAMESPACE__, 'TestController');
        $Code->getNamespace()->appendUse(AbstractConsole::class);
        $Code->getNamespace()->appendUse(EventManagerAwareInterface::class);
        $Code->getClass()->extend('AbstractConsole');
        $Code->getClass()->appendImplement('EventManagerAwareInterface');
        // var_dump($Code->toCode());

    }

    private function readline($prompt)
    {
        $input = readline($prompt . ': ');
        if(isset($input[0])) {
            readline_add_history($input);
        }
        return trim($input);
    }

    public function getDescription()
    {
        return 'module generator';
    }

    public function getHelp()
    {
        return 'need some help';
    }
}
