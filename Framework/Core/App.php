<?php

namespace Framework\Core;

use Framework\Core\Interfaces\AppInterface;
use Framework\Core\ErrorHandler;
use Framework\Core\ViewModel\ViewModelManager;

class App implements AppInterface
{
    const ERROR_NEED_GLOBAL_CONFIG = "error: need global config";
    //
    const DEFAULT_ROUTE = "Framework\Route\RouteModel";
    const DEFAULT_CONTROLLER_NAMESPACE = "Framework\Controller";
    const DEFAULT_MODEL_NAMESPACE = "Framework\Model";
    const DEFAULT_VIEWMODEL_NAMESPACE = "Framework\ViewModel";
    
    static private $globalConfig = null;

    static function setGlobalConfig($config)
    {
        self::$globalConfig = $config;
    }

    static public function run()
    {
        if(empty(self::$globalConfig)) {
            throw new Exception(self::ERROR_NEED_GLOBAL_CONFIG);
        }
        $useErrorHandler = self::$globalConfig->getConfig("ErrorHandler", true);
        if($useErrorHandler) {
            ErrorHandler::setup();
        }
        ViewModelManager::setNamespace(self::$globalConfig->getConfig("viewModelNamespace", self::DEFAULT_VIEWMODEL_NAMESPACE));
        ViewModelManager::setTemplateDir(self::$globalConfig->getConfig("templateDir", ROOT_DIR . str_replace('\\', '/', self::DEFAULT_VIEWMODEL_NAMESPACE)));
        $routeName = self::$globalConfig->getConfig("route", self::DEFAULT_ROUTE);
        $routeModel = self::getRouteModel($routeName);
        list($controllerName, $action, $param) = $routeModel->dispatch();
        $controller = self::getController($controllerName);
        $controller->callActionFlow($action, $param);
    }

    static public function getController($controller)
    {
        $controllerNamespace = self::$globalConfig->getConfig("controllerNamespace", self::DEFAULT_CONTROLLER_NAMESPACE);
        $controller = ucfirst($controller) . "Controller";
        $controllerLabel = $controllerNamespace . "\\" . $controller;
        return $controllerLabel::getSingleton();
    }

    static public function getModel($model)
    {
        $modelNamespace = self::$globalConfig->getConfig("modelNamespace", self::DEFAULT_MODEL_NAMESPACE);
        $model = ucfirst($model) . "Model";
        $modelLabel = $modelNamespace . "\\" . $model;
        return $modelLabel::getSingleton();        
    }
    
    static public function getGlobalConfig($key)
    {
        return self::$$globalConfig->getConfig($key);
    }

    static public function getFormModel($formModelName)
    {
        
    }

    static public function getRouteModel($routeModelName = null)
    {
        if($routeModelName === null) {
            $routeModelName = self::$globalConfig->getConfig("route", self::DEFAULT_ROUTE);
        }
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
