<?php
declare(strict_types=1);

namespace Framework\Module\Cngo\Console\Helper\Generator;

use Framework\Controller\AbstractConsole;
use Framework\Controller\AbstractController;
use Framework\Module\Cngo\Admin\Controller\AbstractAdminController;
use Framework\ViewModel\ViewModelManager;
use Framework\ViewModel\AbstractViewModel;
use Framework\Service\CodeService\CodeServiceAwareInterface;
use Framework\Module\Cngo\Admin\View\Layout\AdminPageLayout;
use CodeService\Code\Wrapper\AbstractWrapper;
use Framework\Repository\EntityManagerAwareInterface;

class Generator implements
    GeneratorInterface,
    CodeServiceAwareInterface,
    EntityManagerAwareInterface
{
    use \Framework\Service\CodeService\CodeServiceAwareTrait;
    use \Framework\Repository\EntityManagerAwareTrait;

    private $testMode = false;
    private $buffer = [];

    private $moduleInfo = [
        'path' => [
            ROOT_DIR, 'Framework', 'Module',
        ],
        'namespace' => null,
        'type' => null,
        'entity' => '',
        'useAwareInterface' => false,
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

    public function generateConsole()
    {
        return $this;
    }

    public function generateController()
    {
        $moduleInfo = $this->getModuleInfo();
        $type = $moduleInfo['type'];
        $Namespace = $moduleInfo['namespace'];
        if ($Namespace) {
            $namespace = '/' . lcfirst($Namespace);
            $Namespace = '/' . $Namespace;
        }
        $path = str_replace([DIRECTORY_SEPARATOR, '\\'], '/', $moduleInfo['path']);
        $controller = $moduleInfo['controller'];
        $viewModel = str_replace('Controller', 'ViewModel', $controller);
        $template = lcfirst(str_replace('Controller', '', $controller));
        $moduleInfo['controller'] = $controller;
        $moduleInfo['viewModel'] = $viewModel;
        $moduleInfo['template'] = $template;
        $this->setModuleInfo($moduleInfo);
        $ControllerPrefix = "Controller/$type/";
        $ViewModelPrefix = "Controller/$type/";
        $templatePrefix = "Controller/$type/";
        $ControllerPathfix = "/Controller$Namespace";
        $ViewModelPathfix = "/View/ViewModel$Namespace";
        $templatePathfix = "/View/template$namespace";
        //
        $Controller = $this->getCodeTemplate("$ControllerPrefix/Controller.php");
        $ControllerPath = $path . "$ControllerPathfix/$controller.php";
        $this->addBuffer($ControllerPath, $Controller);
        $ViewModel = $this->getCodeTemplate("$ViewModelPrefix/ViewModel.php");
        $ViewModelPath = $path . "$ViewModelPathfix/$viewModel.php";
        $this->addBuffer($ViewModelPath, $ViewModel);
        $Template = $this->getCodeTemplate("$templatePrefix/template.phtml");
        $TemplatePath = $path . "$templatePathfix/$template.phtml";
        $this->addBuffer($TemplatePath, $Template);
        return $this;
    }

    public function generateCrud()
    {
        $moduleInfo = $this->getModuleInfo();
        $type = $moduleInfo['type'];
        $Namespace = $moduleInfo['namespace'];
        if ($Namespace) {
            $namespace = '/' . lcfirst($Namespace);
            $Namespace = '/' . $Namespace;
        }
        $path = str_replace([DIRECTORY_SEPARATOR, '\\'], '/', $moduleInfo['path']);
        $ControllerPrefix = "Crud/$type/Controller";
        $ViewModelPrefix = "Crud/$type/ViewModel";
        $templatePrefix = "Crud/$type/template";
        $ControllerPathfix = "/Controller$Namespace";
        $ViewModelPathfix = "/View/ViewModel$Namespace";
        $templatePathfix = "/View/template$namespace";
        // List
        $listController = $this->getCodeTemplate("$ControllerPrefix/ListController.php");
        $listControllerPath = $path . "$ControllerPathfix/ListController.php";
        $this->addBuffer($listControllerPath, $listController);
        $listViewModel = $this->getCodeTemplate("$ViewModelPrefix/ListViewModel.php");
        $listViewModelPath = $path . "$ViewModelPathfix/ListViewModel.php";
        $this->addBuffer($listViewModelPath, $listViewModel);
        $listTemplate = $this->getCodeTemplate("$templatePrefix/list.phtml");
        $listTemplatePath = $path . "$templatePathfix/list.phtml";
        $this->addBuffer($listTemplatePath, $listTemplate);
        // Register
        $registerController = $this->getCodeTemplate("$ControllerPrefix/RegisterController.php");
        $registerControllerPath = $path . "$ControllerPathfix/RegisterController.php";
        $this->addBuffer($registerControllerPath, $registerController);
        $registerViewModel = $this->getCodeTemplate("$ViewModelPrefix/RegisterViewModel.php");
        $registerViewModelPath = $path . "$ViewModelPathfix/RegisterViewModel.php";
        $this->addBuffer($registerViewModelPath, $registerViewModel);
        $registerTemplate = $this->getCodeTemplate("$templatePrefix/register.phtml");
        $registerTemplatePath = $path . "$templatePathfix/register.phtml";
        $this->addBuffer($registerTemplatePath, $registerTemplate);
        // Edit
        $editController = $this->getCodeTemplate("$ControllerPrefix/EditController.php");
        $editControllerPath = $path . "$ControllerPathfix/EditController.php";
        $this->addBuffer($editControllerPath, $editController);
        $editViewModel = $this->getCodeTemplate("$ViewModelPrefix/EditViewModel.php");
        $editViewModelPath = $path . "$ViewModelPathfix/EditViewModel.php";
        $this->addBuffer($editViewModelPath, $editViewModel);
        $editTemplate = $this->getCodeTemplate("$templatePrefix/edit.phtml");
        $editTemplatePath = $path . "$templatePathfix/edit.phtml";
        $this->addBuffer($editTemplatePath, $editTemplate);
        // DELETE
        $deleteController = $this->getCodeTemplate("$ControllerPrefix/DeleteController.php");
        $deleteControllerPath = $path . "$ControllerPathfix/DeleteController.php";
        $this->addBuffer($deleteControllerPath, $deleteController);
        $deleteViewModel = $this->getCodeTemplate("$ViewModelPrefix/DeleteViewModel.php");
        $deleteViewModelPath = $path . "$ViewModelPathfix/DeleteViewModel.php";
        $this->addBuffer($deleteViewModelPath, $deleteViewModel);
        $deleteTemplate = $this->getCodeTemplate("$templatePrefix/delete.phtml");
        $deleteTemplatePath = $path . "$templatePathfix/delete.phtml";
        $this->addBuffer($deleteTemplatePath, $deleteTemplate);
        return $this;
    }

    public function generateEntity()
    {
        $moduleInfo = $this->getModuleInfo();
        $tableName = $moduleInfo['table'];
        $Namespace = $moduleInfo['namespace'] . '\\Entity';
        $path = str_replace([DIRECTORY_SEPARATOR, '\\'], '/', $moduleInfo['path']);
        $EntityPath = $path . '/Entity';
        $em = $this->getEntityManager();
        $em->getConnection()->getDatabasePlatform()->registerDoctrineTypeMapping('set', 'string');
        $em->getConnection()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
        $driver = new \Doctrine\ORM\Mapping\Driver\DatabaseDriver(
            $em->getConnection()->getSchemaManager()
        );
        $em->getConfiguration()->setMetadataDriverImpl($driver);
        $cmf = new \Doctrine\ORM\Tools\DisconnectedClassMetadataFactory($em);
        $cmf->setEntityManager($em);
        $classes = $driver->getAllClassNames();
        $metadata = $cmf->getAllMetadata();
        $metadata = array_filter($metadata, function ($Meta) use ($tableName, $Namespace) {
            $ret = $Meta->getTableName() === $tableName;
            if ($ret) {
                $Meta->name = $Namespace . '\\' . $Meta->name;
            }
            return $ret;
        });
        $generator = new \Doctrine\ORM\Tools\EntityGenerator();
        $generator->setUpdateEntityIfExists(true);
        $generator->setGenerateStubMethods(true);
        $generator->setGenerateAnnotations(true);
        $generator->generate($metadata, ROOT_DIR);
        return $this;
    }

    public function generateModule()
    {
        $moduleInfo = $this->getModuleInfo();
        $useAwareInterface = $moduleInfo['useAwareInterface'];
        $type = $moduleInfo['type'];
        $Namespace = $moduleInfo['namespace'];
        $ModulePathfix = $Namespace;
        $module = $moduleInfo['module'];
        if ($Namespace) {
            $namespace = '/' . lcfirst($Namespace);
            $Namespace = '/' . $Namespace;
        }
        $path = str_replace([DIRECTORY_SEPARATOR, '\\'], '/', $moduleInfo['path']);
        // common module
        $Module = $this->getCodeTemplate("Module/Module.php");
        $ModulePath = $path . "$ModulePathfix/$module.php";
        $this->addBuffer($ModulePath, $Module);
        // common interface
        $interface = $this->getCodeTemplate("Module/ModuleInterface.php");
        $interfacePath = $path . "$ModulePathfix/{$module}Interface.php";
        $this->addBuffer($interfacePath, $interface);
        if ($useAwareInterface) {
            $interface = $this->getCodeTemplate("Module/AwareInterface.php");
            $interfacePath = $path . "$ModulePathfix/{$module}AwareInterface.php";
            $this->addBuffer($interfacePath, $interface);
            $trait = $this->getCodeTemplate("Module/AwareTrait.php");
            $traitPath = $path . "$ModulePathfix/{$module}AwareTrait.php";
            $this->addBuffer($traitPath, $trait);
        }
        return $this;
    }

    private function getCodeTemplate($file, $codeFlag = false)
    {
        $moduleInfo = $this->getModuleInfo();
        $codeTemplate = file_get_contents(__DIR__ . "/CodeTemplate/" . $file);
        $namespace = $moduleInfo['namespace'];
        $Namespace = $namespace;
        $ns = '';
        if ($namespace) {
            $namespace = '/' . lcfirst($namespace);
            $ns = preg_replace('/\w+/', '..', $namespace);
        }
        if ($Namespace) {
            $Namespace = '\\' . $Namespace;
        }
        $search = [
            '{Module}', '{Namespace}', '{namespace}',
            '{Entity}', '{entity}', '{ns}',
            '{Controller}', '{ViewModel}', '{template}'
        ];
        $replace = [
            $moduleInfo['module'] ?? '', $Namespace, $namespace,
            $moduleInfo['entity'] ?? '', lcfirst($moduleInfo['entity']), $ns,
            $moduleInfo['controller'] ?? '', $moduleInfo['viewModel'] ?? '', $moduleInfo['template'] ?? '',
        ];
        $codeTemplate = str_replace($search, $replace, $codeTemplate);
        if ($codeFlag) {
            $codeTemplate = $this->getCodeService()->analysisCode($codeTemplate);
        }
        return $codeTemplate;
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
        $file = str_replace(['\\', DIRECTORY_SEPARATOR], '/', $file);
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
