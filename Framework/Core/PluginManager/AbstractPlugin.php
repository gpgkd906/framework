<?php

namespace Framework\Core\PluginManager;

abstract class AbstractPlugin
{
    static private $instance;
    
    public $listeners = [];

    public function getInstallInfo()
    {
        
    }

    public function getListeners()
    {
        return $this->listeners;
    }

    static public function getSingleton() {
        $pluginName = get_called_class();
        if(!isset(self::$instance[$pluginName])) {
            self::$instance[$pluginName] = new $pluginName();
        }
        return self::$instance[$pluginName];
    }

    public function init($pluginManager)
    {
        foreach($this->listeners as $event => $action) {
            $pluginManager->addEventListener($event, [$this, $action]);
        }
    }

}
