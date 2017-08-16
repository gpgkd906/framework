<?php
/**
 * PHP version 7
 * File ObjectManager.php
 * 
 * @category Interface
 * @package  Framework\ObjectManager
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
declare(strict_types=1);

namespace Framework\ObjectManager;

use Framework\ObjectManager\ObjectManagerAwareInterface;
use Framework\ObjectManager\FactoryInterface;
use Framework\ObjectManager\SingletonInterface;
use Exception;

/**
 * Interface ObjectManager
 * 
 * @category Interface
 * @package  Framework\ObjectManager
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
class ObjectManager implements ObjectManagerInterface, SingletonInterface
{
    use SingletonTrait;

    private $objectFactory = [];
    private $sharedObject = [];
    private $dependencySetter = [];
    private $injectDependencys = [];

    /**
     * Constructor
     */
    private function __construct()
    {
        $this->set(ObjectManagerInterface::class, $this);
    }

    /**
     * Method init
     *
     * @return this
     */
    public function init()
    {
        /**
         * Method exportGlobalObject
         *
         * @return void
         */
        $this->_exportGlobalObject();
        $this->_exportModuleObject();
        $this->_initGlobalObject();
        $this->_initModuleObject();
        return $this;
    }

    /**
     * Method get
     *
     * @param string $name    shareObjectName
     * @param class  $factory ObjectOrFactory
     * 
     * @return Object
     */
    public function get($name, $factory = null)
    {
        if (isset($this->sharedObject[$name])) {
            return $this->sharedObject[$name];
        }
        $Object = $this->create($name, $factory);
        if ($Object) {
            $this->sharedObject[$name] = $Object;
        }
        return $Object;
    }

    /**
     * Method set
     *
     * @param string $name   shareObjectName
     * @param Object $Object Object
     * 
     * @return this
     */
    public function set($name, $Object)
    {
        $this->sharedObject[$name] = $Object;
        return $this;
    }

    /**
     * Method create
     *
     * @param string $name    shareObjectName
     * @param class  $factory ObjectOrFactory
     * 
     * @return Object
     */
    public function create($name, $factory = null)
    {
        $Object = null;
        if ($factory === null) {
            if (isset($this->objectFactory[$name])) {
                $factory = $this->objectFactory[$name];
            } else {
                $factory = $name;
            }
        }
        if (is_subclass_of($factory, FactoryInterface::class)) {
            $ObjectFactory = new $factory;
            $Object = $ObjectFactory->create($this);
        } elseif (is_subclass_of($factory, SingletonInterface::class)) {
            $Object = $factory::getSingleton();
        } else {
            if (class_exists($factory)) {
                $Object = new $factory;
            } else {
                return null;
            }
        }
        $this->injectDependency($Object);
        return $Object;
    }

    /**
     * AwareInterface-base auto dependency-injection
     *
     * @param Object $Object Object
     * 
     * @return this
     */
    public function injectDependency($Object)
    {
        foreach (class_implements($Object) as $interface) {
            if (strpos($interface, 'AwareInterface') && $dependencySetter = $this->_getDependencySetter($interface)) {
                if (isset($this->injectDependencys[$interface])) {
                    list($injectDependency, $injectDependencyInterface) = $this->injectDependencys[$interface];
                } else {
                    $injectDependency = str_replace('AwareInterface', '', $interface);
                    $injectDependencyInterface = $injectDependency . 'Interface';
                    $this->injectDependencys[$interface] = [$injectDependency, $injectDependencyInterface];
                }
                $dependency = null;
                if (isset($this->sharedObject[$injectDependencyInterface])) {
                    $dependency = $this->sharedObject[$injectDependencyInterface];
                } elseif (isset($this->sharedObject[$injectDependency])) {
                    $dependency = $this->sharedObject[$injectDependency];
                } else {
                    if (class_exists($injectDependency)) {
                        $dependency = $this->get($injectDependencyInterface, $injectDependency);
                    } elseif (interface_exists($injectDependencyInterface)) {
                        $dependency = $this->get($injectDependencyInterface);
                    } else {
                        $dependency = $this->get($injectDependency);
                    }
                }
                if ($dependency) {
                    call_user_func([$Object, $dependencySetter], $dependency);
                }
            }
        }
        return $this;
    }

    /**
     * Method _getDependencySetter
     *
     * @param string $interface AwareInterface
     * 
     * @return string setter
     */
    private function _getDependencySetter($interface)
    {
        if (isset($this->dependencySetter[$interface])) {
            return $this->dependencySetter[$interface];
        }
        foreach (get_class_methods($interface) as $method) {
            if (strpos($method, 'set') === 0) {
                $this->dependencySetter[$interface] = $method;
                return $method;
            }
        }
        return;
    }

    /**
     * Method exportGlobalObject
     *
     * @return void
     */
    private function _exportGlobalObject()
    {
        foreach (glob(ROOT_DIR . 'Framework/*/export.php') as $objectExporter) {
            include $objectExporter;
        }
    }

    /**
     * Method exportModuleObject
     *
     * @return void
     */
    private function _exportModuleObject()
    {
        foreach (glob(ROOT_DIR . 'Framework/Module/*/*/export.php') as $moduleExporter) {
            include $moduleExporter;
        }
    }

    /**
     * Method initGlobalObject
     *
     * @return void
     */
    private function _initGlobalObject()
    {
        foreach (glob(ROOT_DIR . 'Framework/*/index.php') as $ObjectEntry) {
            include $ObjectEntry;
        }
    }

    /**
     * Method initModuleObject
     *
     * @return void
     */
    private function _initModuleObject()
    {
        foreach (glob(ROOT_DIR . 'Framework/Module/*/*/index.php') as $moduleEntry) {
            include $moduleEntry;
        }
    }

    /**
     * Method export
     *
     * @param class $Objectfactories ObjectFactoryClasses
     * 
     * @return void
     */
    public function export($Objectfactories)
    {
        foreach ($Objectfactories as $ObjectName => $factory) {
            $this->objectFactory[$ObjectName] = $factory;
        }
    }
}
