<?php

namespace Framework\Module\Cngo\Console\Helper\Generator;

use Framework\Controller\AbstractConsole;
use Framework\Controller\AbstractController;
use Framework\Module\Cngo\Admin\Controller\AbstractAdminController;
use Framework\ViewModel\ViewModelManager;
use Framework\ViewModel\AbstractViewModel;
use Framework\Service\CodeService\CodeServiceAwareInterface;
use Framework\Module\Cngo\Admin\View\Layout\AdminPageLayout;
use CodeService\Code\Wrapper\AbstractWrapper;

class Generator implements GeneratorInterface, CodeServiceAwareInterface
{
    use \Framework\Service\CodeService\CodeServiceAwareTrait;

    const ROUTER_ADMIN = 'Admin';
    const ROUTER_FRONT = 'Front';
    const ROUTER_CONSOLE = 'Console';

    const CRUD_LIST = 'List';
    const CRUD_REGISTER = 'Register';
    const CRUD_EDIT = 'Edit';
    const CRUD_DELETE = 'Delete';

    private $testMode = false;
    private $buffer = [];

    private $moduleInfo = [
        'path' => [
            ROOT_DIR, 'Framework', 'Module',
        ],
        'namespace' => [
            \Framework\Module::class
        ],
        'type' => null,
        'router' => [],
    ];

    public function setTestMode($testMode)
    {
        $this->testMode = $testMode;
    }

    public function getModuleInfo()
    {
        return $this->moduleInfo;
    }

    public function setModuleInfo($moduleInfo)
    {
        $this->moduleInfo = $moduleInfo;
        return $this;
    }

    public function generateModule()
    {
        $path = $moduleInfo['path'];
        $namespace = $moduleInfo['namespace'];
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
        return $this;
    }

    public function generateRoute()
    {
        $moduleInfo = $this->getModuleInfo();
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
        $this->addBuffer($routerInjector, $routerCode);
        return $this;
    }

    public function generateConsole()
    {
        $moduleInfo = $this->getModuleInfo();
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
            if (isset($route['onConsoleGenerated'])) {
                $Code = call_user_func($route['onConsoleGenerated'], $Code);
            }
            $this->addBuffer($controllerPath, $Code);
        }
        return $this;
    }

    public function generateController()
    {
        $moduleInfo = $this->getModuleInfo();
        $CodeService = $this->getCodeService();
        $path = $moduleInfo['path'];
        $namespace = $moduleInfo['namespace'];
        foreach ($moduleInfo['router'] as $route) {
            $controller = $route['controller'];
            $controllerPath = $path . '/' . $controller . '.php';
            $Code = $CodeService->createCode($namespace, $controller);
            $Code->getNamespace()->appendUse(AbstractAdminController::class);
            $Code->getNamespace()->appendUse(ViewModelManager::class);
            list($viewNamespace, $viewModel) = $this->generateViewModel($controller, $moduleInfo);
            $Code->getNamespace()->appendUse($viewNamespace . '\\' . $viewModel);
            $Code->getClass()->extend('AbstractAdminController');
            $Code->getClass()->appendMethod('index');
            $Code->getClass()->getMethod('index')->setReturn("ViewModelManager::getViewModel(['viewModel' => $viewModel::class])");
            if (isset($route['onControllerGenerated'])) {
                $Code = call_user_func($route['onControllerGenerated'], $Code);
            }
            $this->addBuffer($controllerPath, $Code);
        }
        return $this;
    }

    public function generateViewModel($controller)
    {
        $moduleInfo = $this->getModuleInfo();
        $path = $moduleInfo['path'];
        $namespace = $moduleInfo['namespace'];
        $viewModel = str_replace('Controller', 'ViewModel', $controller);
        $template = strtolower(str_replace('Controller', '', $controller));
        $templatePath = str_replace('Controller', 'View\template', $path);
        $path = str_replace('Controller', 'View\ViewModel', $path);
        $viewNamespace = str_replace('Controller', 'View\ViewModel', $namespace);
        $viewModelPath = $path . '/' . $viewModel . '.php';
        $templatePath = $templatePath . '/' . strtolower($template) . '.phtml';
        list($templatePath, $partTemplatePath) = explode('View\template', $templatePath);
        $deepChecker = str_replace('\\', '/', \Framework\Module::class);
        $deep = explode($deepChecker, $templatePath)[1];
        $deep = preg_replace('/\w+/', '..', $deep);
        $deep = rtrim($deep, '/');
        $deep = str_replace(['\\', '//'], '/', $deep);
        $templatePath .= 'View/template' . strtolower($partTemplatePath);
        $CodeService = $this->getCodeService();
        $ViewModelCode = $CodeService->createCode($viewNamespace, $viewModel);
        $ViewModelCode->getNamespace()->appendUse(AbstractViewModel::class);
        $ViewModelCode->getNamespace()->appendUse(AdminPageLayout::class);
        $ViewModelCode->getClass()->extend('AbstractViewModel');
        $ViewModelCode->getClass()->appendProperty('template', '/template' . strtolower($partTemplatePath), 'protected');
        $ViewModelCode->getClass()->appendProperty('config', ["layout" => '\\' . AdminPageLayout::class ], 'protected');
        $ViewModelCode->getClass()->appendMethod('getTemplateDir');
        $ViewModelCode->getClass()->getMethod('getTemplateDir')->setReturn("__DIR__ . '$deep'");
        if (isset($route['onViewModelGenerated'])) {
            $ViewModelCode = call_user_func($route['onViewModelGenerated'], $ViewModelCode);
        }
        $this->addBuffer($viewModelPath, $ViewModelCode);
        $templateCode = <<<TEMPLATE
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Template</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
</div>
<!-- /.container-fluid -->
TEMPLATE;
        if (isset($route['onTemplateGenerated'])) {
            $templateCode = call_user_func($route['onTemplateGenerated'], $templateCode);
        }
        $this->addBuffer($templatePath, $templateCode);
        return [$viewNamespace, $viewModel];
    }

    public function generateCrud()
    {
        $moduleInfo = $this->getModuleInfo();
        $namespace = $moduleInfo['namespace'];
        if (isset($moduleInfo['crud']['name']) && $moduleInfo['crud']['name']) {
            $namespace .= '\\' . $moduleInfo['crud']['name'];
        }
        $moduleInfo['namespace'] = $namespace;
        // List
        $moduleInfo['router'] = [
            ['controller' => self::CRUD_LIST . 'Controller']
        ];
        $this->setModuleInfo($moduleInfo);
        $this->generateController();
        // Register
        $moduleInfo['router'] = [
            ['controller' => self::CRUD_REGISTER . 'Controller']
        ];
        $this->setModuleInfo($moduleInfo);
        $this->generateController();
        // Edit
        $moduleInfo['router'] = [
            ['controller' => self::CRUD_EDIT . 'Controller']
        ];
        $this->setModuleInfo($moduleInfo);
        $this->generateController();
        // DELETE
        $moduleInfo['router'] = [
            ['controller' => self::CRUD_DELETE . 'Controller']
        ];
        $this->setModuleInfo($moduleInfo);
        $this->generateController();
        return $this;
    }

    private function addBuffer($file, $Contents)
    {
        $this->buffer[] = [$file, $Contents];
    }

    public function flush()
    {
        foreach ($this->buffer as list($file, $contents)) {
            if ($contents instanceof AbstractWrapper) {
                $contents = $contents->toCode();
            }
            $this->write($file, $contents);
        }
    }

    private function write($file, $Contents)
    {
        $file = str_replace(['\\', '//'], '/', $file);
        if (is_file($file)) {
            echo 'file exists: ' . $file, PHP_EOL;
            echo 'if you *really* want addBuffer the file, delete it', PHP_EOL;
            echo '    rm ' . $file, PHP_EOL;
            echo '...skip...', PHP_EOL;
            return false;
        }
        if ($this->testMode) {
            var_dump($file, $Contents);
        } else {
            $dir = dirname($file);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            file_put_contents($file, $Contents);
            echo 'file generated: ' . $file, PHP_EOL;
        }
    }
}
