<?php

namespace Framework\ObjectManager;

use Framework\ObjectManager\ObjectManagerAwareInterface;
use Framework\ObjectManager\FactoryInterface;
use Framework\ObjectManager\SingletonInterface;
use Exception;

class ObjectManager implements ObjectManagerInterface, SingletonInterface
{
    use SingletonTrait;

    /**
    *
    * @api
    * @var mixed $application
    * @access private
    * @link
    */
    private $objectFactory = [];
    private $sharedObject = [];
    private $dependencySetter = [];

    private function __construct()
    {
        $this->set(ObjectManagerInterface::class, $this);
    }

    public function init()
    {
        $this->exportGlobalObject();
        $this->exportModuleObject();
        $this->initModuleObject();
        $this->initGlobalObject();
    }

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

    public function set($name, $Object)
    {
        $this->sharedObject[$name] = $Object;
    }

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
            $Object = $ObjectFactory->create();
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
    *  AwareInterface-base auto dependency-injection
    */
    public function injectDependency($Object)
    {
        foreach (class_implements($Object) as $interface) {
            if (strpos($interface, 'AwareInterface') && $dependencySetter = $this->getDependencySetter($interface)) {
                $injectDependency = str_replace('AwareInterface', '', $interface);
                $injectDependencyInterface = $injectDependency . 'Interface';
                $dependency = null;
                if (isset($this->sharedObject[$injectDependencyInterface])) {
                    $dependency = $this->sharedObject[$injectDependencyInterface];
                } elseif (isset($this->sharedObject[$injectDependency])) {
                    $dependency = $this->sharedObject[$injectDependency];
                } else {
                    $dependency = $this->get($injectDependencyInterface, $injectDependency);
                }
                if ($dependency) {
                    call_user_func([$Object, $dependencySetter], $dependency);
                }
            }
        }
    }

    private function getDependencySetter($interface)
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

    private function exportGlobalObject()
    {
        foreach (glob(ROOT_DIR . 'Framework/*/export.php') as $objectExporter) {
            require $objectExporter;
        }
    }

    private function exportModuleObject()
    {
        foreach (glob(ROOT_DIR . 'Framework/Module/*/*/export.php') as $moduleExporter) {
            require $moduleExporter;
        }
    }

    private function initGlobalObject()
    {
        foreach (glob(ROOT_DIR . 'Framework/*/index.php') as $ObjectEntry) {
            require $ObjectEntry;
        }
    }

    private function initModuleObject()
    {
        foreach (glob(ROOT_DIR . 'Framework/Module/*/*/index.php') as $moduleEntry) {
            require $moduleEntry;
        }
    }

    public function export($Objectfactories)
    {
        foreach ($Objectfactories as $ObjectName => $factory) {
            $this->objectFactory[$ObjectName] = $factory;
        }
    }
}
