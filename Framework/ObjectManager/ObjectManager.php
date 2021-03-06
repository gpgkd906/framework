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
class ObjectManager implements
    ObjectManagerInterface,
    SingletonInterface
{
    use SingletonTrait;

    private $_objectFactory = [];
    private $_sharedObject = [];
    private $_dependencySetter = [];
    private $_injectDependencys = [];

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
     * @codeCoverageIgnore
     * @return this
     */
    public function init()
    {
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
        if (isset($this->_sharedObject[$name])) {
            return $this->_sharedObject[$name];
        }
        $Object = $this->create($name, $factory);
        if ($Object) {
            $this->_sharedObject[$name] = $Object;
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
        $this->_sharedObject[$name] = $Object;
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
            if (isset($this->_objectFactory[$name])) {
                $factory = $this->_objectFactory[$name];
            } else {
                $factory = $name;
            }
        }
        if ($factory instanceof \Closure) {
            $Object = call_user_func($factory);
        } else if (is_subclass_of($factory, FactoryInterface::class)) {
            $Object = (new $factory)->create($this);
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
            // AwareInterfaceが存在、且つset{Object}のメソッドが存在していれば
            if (strpos($interface, 'AwareInterface') && $dependencySetter = $this->_getDependencySetter($interface)) {
                // _injectDependencyの対象classかInterfaceを取得する
                if (isset($this->_injectDependencys[$interface])) {
                    list($injectDependency, $injectDependencyInterface) = $this->_injectDependencys[$interface];
                } else {
                    $injectDependency = str_replace('AwareInterface', '', $interface);
                    $injectDependencyInterface = $injectDependency . 'Interface';
                    $this->_injectDependencys[$interface] = [$injectDependency, $injectDependencyInterface];
                }
                $dependency = null;
                // interface > classの順番で、既にObjectManagerが管理しているObjectがあれば、それを「注入」する
                if (isset($this->_sharedObject[$injectDependencyInterface])) {
                    $dependency = $this->_sharedObject[$injectDependencyInterface];
                } elseif (isset($this->_sharedObject[$injectDependency])) {
                    $dependency = $this->_sharedObject[$injectDependency];
                } else {
                    // もし、ObjectManagerが管理しているObjectが無ければ
                    // $injectDependencyInterface, $injectDependencyを使って、Objectの生成をためす。
                    // Objectの生成順も Interface > class の存在順
                    if (interface_exists($injectDependencyInterface)) {
                        if (class_exists($injectDependency)) {
                            $dependency = $this->get($injectDependencyInterface, $injectDependency);                            
                        } else {
                            $dependency = $this->get($injectDependencyInterface);                            
                        }
                    } elseif (class_exists($injectDependency)) {
                        $dependency = $this->get($injectDependency);
                    } else{
                        // 仮想Interface・classによるInjection
                        ($dependency = $this->get($injectDependencyInterface))
                            || ($dependency = $this->get($injectDependency));
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
        if (isset($this->_dependencySetter[$interface])) {
            return $this->_dependencySetter[$interface];
        }
        foreach (get_class_methods($interface) as $method) {
            if (strpos($method, 'set') === 0) {
                $this->_dependencySetter[$interface] = $method;
                break;
            }
        }
        return $method;
    }

    /**
     * Method exportGlobalObject
     *
     * @codeCoverageIgnore
     * @return void
     */
    private function _exportGlobalObject()
    {
        foreach (glob(ROOT_DIR . 'Framework/*/export.php') as $objectExporter) {
            include $objectExporter;
        }
        foreach (glob(ROOT_DIR . 'Std/*/export.php') as $objectExporter) {
            include $objectExporter;
        }
    }

    /**
     * Method exportModuleObject
     *
     * @codeCoverageIgnore
     * @return void
     */
    private function _exportModuleObject()
    {
        foreach (glob(ROOT_DIR . 'Project/*/*/export.php') as $moduleExporter) {
            include $moduleExporter;
        }
    }

    /**
     * Method initGlobalObject
     *
     * @codeCoverageIgnore
     * @return void
     */
    private function _initGlobalObject()
    {
        foreach (glob(ROOT_DIR . 'Framework/*/index.php') as $ObjectEntry) {
            include $ObjectEntry;
        }
        foreach (glob(ROOT_DIR . 'Std/*/index.php') as $ObjectEntry) {
            include $ObjectEntry;
        }
    }

    /**
     * Method initModuleObject
     *
     * @codeCoverageIgnore
     * @return void
     */
    private function _initModuleObject()
    {
        foreach (glob(ROOT_DIR . 'Project/*/*/index.php') as $moduleEntry) {
            include $moduleEntry;
        }
    }

    /**
     * Method export
     *
     * @param class $Objectfactories _ObjectFactoryClasses
     *
     * @return void
     */
    public function export($Objectfactories)
    {
        foreach ($Objectfactories as $ObjectName => $factory) {
            $this->_objectFactory[$ObjectName] = $factory;
        }
    }

    public function getNamedObjectManager($namespace)
    {

    }
}
