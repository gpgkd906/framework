<?php

namespace Framework\Core;

use Framework\Core\Interfaces\AppInterface;
use Framework\Core\ErrorHandler;
use Framework\Core\ViewModel\ViewModelManager;
use Framework\Core\EventManager\EventManager;
use Framework\Core\App;
use Exception;

class Console Extends App implements AppInterface
{
    const DEFAULT_CONTROLLER_NAMESPACE = "Framework\Console";
    
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
        $routeModel->setIsConsole(true);

        //with console, we do not need view...
        /* ViewModelManager::setNamespace(self::$globalConfig->getConfig("viewModelNamespace", self::DEFAULT_VIEWMODEL_NAMESPACE)); */
        /* ViewModelManager::setTemplateDir(self::$globalConfig->getConfig("templateDir", ROOT_DIR . str_replace('\\', '/', self::DEFAULT_VIEWMODEL_NAMESPACE))); */
        self::$eventManager = new EventManager;
        //plugin
        $pluginManager = self::getPluginManager();
        $pluginManager->initPlugins();
        list($controllerName, $action, $param) = $routeModel->dispatch();
        $controller = self::getController($controllerName);
        $controller->callActionFlow($action, $param);
    }

    static public function getController($controller)
    {
        $controllerNamespace = self::$globalConfig->getConfig("consoleNamespace", self::DEFAULT_CONTROLLER_NAMESPACE);
        $controller = ucfirst($controller) . "Controller";
        $controllerLabel = $controllerNamespace . "\\" . $controller;
        if(!class_exists($controllerLabel)) {
            throw new Exception(sprintf(self::ERROR_INVALID_CONTROLLER_LABEL, $controllerLabel));
        }
        return $controllerLabel::getSingleton();
    }
}
