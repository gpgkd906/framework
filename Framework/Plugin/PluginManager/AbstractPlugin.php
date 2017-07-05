<?php

namespace Framework\Plugin\PluginManager;

use Framework\ObjectManager\SingletonInterface;

abstract class AbstractPlugin implements SingletonInterface
{
    use \Framework\ObjectManager\SingletonTrait;
    
    static private $instance;
    
    public function getInstallInfo()
    {
        
    }

    public function getListeners()
    {
        return $this->listeners;
    }

    public function init($pluginManager)
    {
        foreach ($this->listeners as $event => $action) {
            $pluginManager->addEventListener($event, [$this, $action]);
        }
    }

}
