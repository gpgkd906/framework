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
    private $application = null;

    private $sharedObject = [];
    private $awareSetter = [];

    private function __construct()
    {
        $this->set(ObjectManagerInterface::class, $this);
    }

    public function get($name, $factory = null)
    {
        if (isset($this->sharedObject[$name])) {
            return $this->sharedObject[$name];
        }
        $this->sharedObject[$name] = $this->create($name, $factory);
        return $this->sharedObject[$name];
    }

    public function set($name, $Object)
    {
        $this->sharedObject[$name] = $Object;
    }

    public function create($name, $factory = null)
    {
        $Object = null;
        if ($factory === null) {
            $factory = $name;
        }
        if (is_subclass_of($factory, FactoryInterface::class)) {
            $ObjectFactory = new $factory;
            $Object = $ObjectFactory->create();
        } else {
            $Object = new $factory;
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
                } else if (isset($this->sharedObject[$injectDependency])) {
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
      if (isset($this->awareSetter[$interface])) {
        return $this->awareSetter[$interface];
      }
      foreach (get_class_methods($interface) as $method) {
        if (strpos($method, 'set') !== false) {
          $this->awareSetter[$interface] = $method;
          return $method;
        }
      }
      return;
    }
}