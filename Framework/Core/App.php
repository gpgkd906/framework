<?php

namespace Framework\Core;

use Framework\Core\Interfaces\AppInterface;
use Framework\Core\ErrorHandler;
use Framework\Core\ViewModel\ViewModelManager;
use Framework\Core\EventManager\EventManager;
use Exception;

class App implements AppInterface
{
    const ERROR_NEED_GLOBAL_CONFIG = "error: need global config";
    const ERROR_INVALID_CONTROLLER_LABEL = "error: invalid_controller_label: %s";
    //
    const DEFAULT_PLUGINMANAGER = "Framework\Core\PluginManager\PluginManager";
    const DEFAULT_ROUTE = "Framework\Core\RouteModel";
    const DEFAULT_CONTROLLER_NAMESPACE = "Framework\Controller";
    const DEFAULT_MODEL_NAMESPACE = "Framework\Model";
    const DEFAULT_VIEWMODEL_NAMESPACE = "Framework\ViewModel";
    const DEFAULT_SERVICE_NAMESPACE = "Framework\Service";
    //
    const DEFAULT_TIMEZONE = "Asia/Tokyo";
    static private $globalConfig = null;
    static private $eventManager = null;
    static private $pluginManager = null;

    static public function setGlobalConfig($config)
    {        
        self::$globalConfig = $config;
        if(self::$globalConfig->getConfig('timezon')) {
            date_default_timezone_set(self::$globalConfig->getConfig('timezon'));
        } else {
            date_default_timezone_set(self::DEFAULT_TIMEZONE);
        }
    }

    static public function getGlobalConfig($key = null)
    {
        if($key === null) {
            return self::$globalConfig;
        } else {
            return self::$globalConfig->getConfig($key);
        }
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
        //route
        $routeName = self::$globalConfig->getConfig("route", self::DEFAULT_ROUTE);
        $routeModel = self::getRouteModel($routeName);
        if($routeModel->isFaviconRequest()) {
            $routeModel->sendDummyFavicon();
        }        

        ViewModelManager::setNamespace(self::$globalConfig->getConfig("viewModelNamespace", self::DEFAULT_VIEWMODEL_NAMESPACE));
        ViewModelManager::setTemplateDir(self::$globalConfig->getConfig("templateDir", ROOT_DIR . str_replace('\\', '/', self::DEFAULT_VIEWMODEL_NAMESPACE)));
        //eventManager
        $eventManager = self::getEventManager();
        //plugin
        $pluginManager = self::getPluginManager();
        $pluginManager->initPlugins();
        list($controllerName, $action, $param) = $routeModel->dispatch();
        $controller = self::getController($controllerName);
        $controller->callActionFlow($action, $param);
    }

    static public function getController($controller)
    {
        $controllerNamespace = self::$globalConfig->getConfig("controllerNamespace", self::DEFAULT_CONTROLLER_NAMESPACE);
        $controller = ucfirst($controller) . "Controller";
        $controllerLabel = $controllerNamespace . "\\" . $controller;
        if(!class_exists($controllerLabel)) {
            throw new Exception(sprintf(self::ERROR_INVALID_CONTROLLER_LABEL, $controllerLabel));
        }
        return $controllerLabel::getSingleton();
    }

    static public function getModel($model)
    {
        $modelNamespace = self::$globalConfig->getConfig("modelNamespace", self::DEFAULT_MODEL_NAMESPACE);
        $model = ucfirst($model) . "\\Model";
        $modelLabel = $modelNamespace . "\\" . $model;
        return $modelLabel::getSingleton();        
    }
    
    static public function getRouteModel($routeModelName = null)
    {
        if($routeModelName === null) {
            $routeModelName = self::$globalConfig->getConfig("route", self::DEFAULT_ROUTE);
        }
        return $routeModelName::getSingleton();
    }

    static public function getPluginManager()
    {
        if(self::$pluginManager == null) {
            $pluginManagerName = self::$globalConfig->getConfig("pluginManager", self::DEFAULT_PLUGINMANAGER);
            self::$pluginManager = $pluginManagerName::getSingleton();
        }
        return self::$pluginManager;
    }

    static public function getEventManager()
    {
        if(self::$eventManager === null) {
            self::$eventManager = new EventManager;
        }
        return self::$eventManager;
    }

    static public function getHelper($helperName)
    {
        
    }

    static public function getService($service)
    {
        $serviceNamespace = self::$globalConfig->getConfig("ServiceNamespace", self::DEFAULT_SERVICE_NAMESPACE);
        $service = ucfirst($service) . "Service";
        $serviceLabel = $serviceNamespace . "\\" . $service . "\\" . $service;
        if(!class_exists($serviceLabel)) {
            throw new Exception(sprintf(self::ERROR_INVALID_CONTROLLER_LABEL, $serviceLabel));
        }
        return $serviceLabel::getSingleton();        
    }

    static public function import($namespace, $className)
    {
        
    }
}
