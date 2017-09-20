<?php
/**
 * PHP version 7
 * File Generator.php
 *
 * @category Module
 * @package  Framework\Module\Cngo\Console
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
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

/**
 * Class Generator
 *
 * @category Helper
 * @package  Framework\Module\Cngo\Console
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
class Generator implements
    GeneratorInterface,
    CodeServiceAwareInterface,
    EntityManagerAwareInterface
{
    use \Framework\Service\CodeService\CodeServiceAwareTrait;
    use \Framework\Repository\EntityManagerAwareTrait;

    private $_testMode = false;
    private $_buffer = [];

    private $_moduleInfo = [
        'path' => [
            ROOT_DIR, 'Framework', 'Module',
        ],
        'namespace' => null,
        'type' => null,
        'entity' => '',
        'useAwareInterface' => false,
    ];

    /**
     * Method setTestMode
     *
     * @param boolean $testMode testMode
     *
     * @return this
     */
    public function setTestMode($testMode)
    {
        $this->_testMode = $testMode;
        return $this;
    }

    /**
     * Method getModuleInfo
     *
     * @return Array
     */
    public function getModuleInfo()
    {
        return $this->_moduleInfo;
    }

    /**
     * Method setModuleInfo
     *
     * @param Array $moduleInfo moduleInfo
     *
     * @return this
     */
    public function setModuleInfo($moduleInfo)
    {
        $this->_moduleInfo = $moduleInfo;
        return $this;
    }

    /**
     * Method generateConsole
     *
     * @return this
     */
    public function generateConsole()
    {
        return $this;
    }

    /**
     * Method generateController
     *
     * @return this
     */
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
        $Controller = $this->_getCodeTemplate("$ControllerPrefix/Controller.php");
        $ControllerPath = $path . "$ControllerPathfix/$controller.php";
        $this->_addBuffer($ControllerPath, $Controller);
        $ViewModel = $this->_getCodeTemplate("$ViewModelPrefix/ViewModel.php");
        $ViewModelPath = $path . "$ViewModelPathfix/$viewModel.php";
        $this->_addBuffer($ViewModelPath, $ViewModel);
        $Template = $this->_getCodeTemplate("$templatePrefix/template.phtml");
        $TemplatePath = $path . "$templatePathfix/$template.phtml";
        $this->_addBuffer($TemplatePath, $Template);
        return $this;
    }

    /**
     * Method generateCrud
     *
     * @return this
     */
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
        $listController = $this->_getCodeTemplate("$ControllerPrefix/ListController.php");
        $listControllerPath = $path . "$ControllerPathfix/ListController.php";
        $this->_addBuffer($listControllerPath, $listController);
        $listViewModel = $this->_getCodeTemplate("$ViewModelPrefix/ListViewModel.php");
        $listViewModelPath = $path . "$ViewModelPathfix/ListViewModel.php";
        $this->_addBuffer($listViewModelPath, $listViewModel);
        $listTemplate = $this->_getCodeTemplate("$templatePrefix/list.phtml");
        $listTemplatePath = $path . "$templatePathfix/list.phtml";
        $this->_addBuffer($listTemplatePath, $listTemplate);
        // Register
        $registerController = $this->_getCodeTemplate("$ControllerPrefix/RegisterController.php");
        $registerControllerPath = $path . "$ControllerPathfix/RegisterController.php";
        $this->_addBuffer($registerControllerPath, $registerController);
        $registerViewModel = $this->_getCodeTemplate("$ViewModelPrefix/RegisterViewModel.php");
        $registerViewModelPath = $path . "$ViewModelPathfix/RegisterViewModel.php";
        $this->_addBuffer($registerViewModelPath, $registerViewModel);
        $registerTemplate = $this->_getCodeTemplate("$templatePrefix/register.phtml");
        $registerTemplatePath = $path . "$templatePathfix/register.phtml";
        $this->_addBuffer($registerTemplatePath, $registerTemplate);
        // Edit
        $editController = $this->_getCodeTemplate("$ControllerPrefix/EditController.php");
        $editControllerPath = $path . "$ControllerPathfix/EditController.php";
        $this->_addBuffer($editControllerPath, $editController);
        $editViewModel = $this->_getCodeTemplate("$ViewModelPrefix/EditViewModel.php");
        $editViewModelPath = $path . "$ViewModelPathfix/EditViewModel.php";
        $this->_addBuffer($editViewModelPath, $editViewModel);
        $editTemplate = $this->_getCodeTemplate("$templatePrefix/edit.phtml");
        $editTemplatePath = $path . "$templatePathfix/edit.phtml";
        $this->_addBuffer($editTemplatePath, $editTemplate);
        // DELETE
        $deleteController = $this->_getCodeTemplate("$ControllerPrefix/DeleteController.php");
        $deleteControllerPath = $path . "$ControllerPathfix/DeleteController.php";
        $this->_addBuffer($deleteControllerPath, $deleteController);
        $deleteViewModel = $this->_getCodeTemplate("$ViewModelPrefix/DeleteViewModel.php");
        $deleteViewModelPath = $path . "$ViewModelPathfix/DeleteViewModel.php";
        $this->_addBuffer($deleteViewModelPath, $deleteViewModel);
        $deleteTemplate = $this->_getCodeTemplate("$templatePrefix/delete.phtml");
        $deleteTemplatePath = $path . "$templatePathfix/delete.phtml";
        $this->_addBuffer($deleteTemplatePath, $deleteTemplate);
        return $this;
    }

    /**
     * Method generateEntity
     *
     * @return this
     */
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
        $metadata = array_filter(
            $metadata,
            function ($Meta) use ($tableName, $Namespace) {
                $ret = $Meta->getTableName() === $tableName;
                if ($ret) {
                    $Meta->name = $Namespace . '\\' . $Meta->name;
                }
                return $ret;
            }
        );
        $generator = new \Doctrine\ORM\Tools\EntityGenerator();
        $generator->setUpdateEntityIfExists(true);
        $generator->setGenerateStubMethods(true);
        $generator->setGenerateAnnotations(true);
        $generator->generate($metadata, ROOT_DIR);
        return $this;
    }

    /**
     * Method generateModule
     *
     * @return this
     */
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
        $Module = $this->_getCodeTemplate("Module/Module.php");
        $ModulePath = $path . "$ModulePathfix/$module.php";
        $this->_addBuffer($ModulePath, $Module);
        // common interface
        $interface = $this->_getCodeTemplate("Module/ModuleInterface.php");
        $interfacePath = $path . "$ModulePathfix/{$module}Interface.php";
        $this->_addBuffer($interfacePath, $interface);
        if ($useAwareInterface) {
            $interface = $this->_getCodeTemplate("Module/AwareInterface.php");
            $interfacePath = $path . "$ModulePathfix/{$module}AwareInterface.php";
            $this->_addBuffer($interfacePath, $interface);
            $trait = $this->_getCodeTemplate("Module/AwareTrait.php");
            $traitPath = $path . "$ModulePathfix/{$module}AwareTrait.php";
            $this->_addBuffer($traitPath, $trait);
        }
        return $this;
    }

    /**
     * Method getCodeTemplate
     *
     * @param string  $file     file
     * @param boolean $codeFlag useCodeSerivce
     *
     * @return string|AbstractWrapper
     */
    private function _getCodeTemplate($file, $codeFlag = false)
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
            'Generator', '{Namespace}', '{namespace}',
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

    /**
     * Method _addBuffer
     *
     * @param string                 $file     file
     * @param string|AbstractWrapper $Contents Contents
     *
     * @return this
     */
    private function _addBuffer($file, $Contents)
    {
        $this->_buffer[] = [$file, $Contents];
        return $this;
    }

    /**
     * Method flush
     *
     * @return this
     */
    public function flush()
    {
        foreach ($this->_buffer as list($file, $contents)) {
            if ($contents instanceof AbstractWrapper) {
                $contents = $contents->toCode();
            }
            $this->_write($file, $contents);
        }
        return $this;
    }

    /**
     * Method _write
     *
     * @param string                 $file     file
     * @param string|AbstractWrapper $Contents Contents
     *
     * @return this
     */
    private function _write($file, $Contents)
    {
        $file = str_replace(['\\', DIRECTORY_SEPARATOR], '/', $file);
        if (is_file($file)) {
            echo 'file exists: ' . $file, PHP_EOL;
            echo 'if you *really* want addBuffer the file, delete it', PHP_EOL;
            echo '    rm ' . $file, PHP_EOL;
            echo '...skip...', PHP_EOL;
            return false;
        }
        if ($this->_testMode) {
            var_dump($file, $Contents);
        } else {
            $dir = dirname($file);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            file_put_contents($file, $Contents);
            echo 'file generated: ' . $file, PHP_EOL;
        }
        return $this;
    }
}
