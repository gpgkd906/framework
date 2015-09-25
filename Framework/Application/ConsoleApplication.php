<?php

namespace Framework\Application;

use Framework\Log\ErrorHandler;
use Exception;

class ConsoleApplication Extends AbstractApplication
{
    const DEFAULT_ROUTE = "Framework\Route\ConsoleRouteModel";
    const DEFAULT_CONTROLLER_NAMESPACE = "Framework\Console";
    
    public function run()
    {
        ErrorHandler::setHtmlFormatFlag(false);
        $config = $this->getConfig();
        //route
        $routeName = $config->getConfig("console_route", self::DEFAULT_ROUTE);
        $routeModel = $this->getRouteModel($routeName);

        $request = $routeModel->dispatch();
        $controller = $this->getController($request['controller']);
        $controller->callActionFlow($request['action'], $request['param']);
    }

    public function getController($controller)
    {
        $config = $this->getConfig();
        $controllerNamespace = $config->getConfig("consoleNamespace", self::DEFAULT_CONTROLLER_NAMESPACE);
        $controller = ucfirst($controller) . "Controller";
        $controllerLabel = $controllerNamespace . "\\" . $controller;
        if(!class_exists($controllerLabel)) {
            throw new Exception(sprintf(self::ERROR_INVALID_CONTROLLER_LABEL, $controllerLabel));
        }
        return $controllerLabel::getSingleton();
    }

    public function redirect($controller, $action = null, $param = null)
    {
        throw new Exception("can not redirect in console mode");
    }
}
