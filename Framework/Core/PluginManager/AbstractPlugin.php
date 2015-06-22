<?php

namespace Framework\Core\PluginManager;

abstract class AbstractPlugin
{
    use \Framework\Core\SingletonTrait;
    
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
        foreach($this->listeners as $event => $action) {
            $pluginManager->addEventListener($event, [$this, $action]);
        }
    }

}
