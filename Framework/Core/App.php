<?php

namespace Framework\Core;

use Framework\Core\Interfaces\AppInterface;

class App implements AppInterface
{
    static public function run($config)
    {
        var_dump($config);
    }

    static public function getController($controllerName)
    {
        
    }

    static public function getModel($modelName)
    {
        
    }

    static public function getViewModel($viewModelName)
    {
        
    }

    static public function getFormModel($formModelName)
    {
        
    }

    static public function getRouter()
    {
        
    }

    static public function getHelper($helperName)
    {
        
    }

    static public function getService($serviceName)
    {
        
    }

    static public function getPlugin($pluginName)
    {
        
    }

    static public function getModule($moduleName)
    {
        
    }

    static public function import($namespace, $className)
    {
        
    }
}
