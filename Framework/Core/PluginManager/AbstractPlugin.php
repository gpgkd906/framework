<?php

namespace Framework\Core\PluginManager;

abstract class AbstractPlugin
{
    static private $instance;
    
    static public function getSingleton() {
        $pluginName = get_called_class();
        if(!isset(self::$instance[$pluginName])) {
            self::$instance[$pluginName] = new $pluginName();
        }
        return self::$instance[$pluginName];
    }

    public $actions = [];

    public function init($pluginManager)
    {
        foreach($this->actions as $event => $action) {
            $pluginManager->addEventListener($event, [$this, $action]);
        }
    }

}
