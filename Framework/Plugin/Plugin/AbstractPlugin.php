<?php

namespace Framework\Plugin\Plugin;

abstract class AbstractPlugin
{
    use \Framework\Application\SingletonTrait;
    
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
