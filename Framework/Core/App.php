<?php

namespace Framework\Core;

use Framework\Core\Interfaces\AppInterface;

class App implements AppInterface
{
    static private $globalConfig = null;

    static function setGlobalConfig($config)
    {
        self::$globalConfig = $config;
    }

    static public function run()
    {
        $routeName = self::$globalConfig->getConfig("route", "Framework\Route\RouteModel");
        $routeModel = self::getRouteModel($routeName);
        list($controllerName, $action, $param) = $routeModel->dispatch();
        $controller = self::getController($controllerName);
        $controller->process($action, $param);
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

    static public function getRouteModel($routeModelName)
    {
        return $routeModelName::getSingleton();
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
