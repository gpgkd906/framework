<?php

namespace Framework\Application;

use Framework\Log\ErrorHandler;
use Exception;

class ConsoleApplication Extends AbstractApplication
{
    const DEFAULT_ROUTE = "Console";
    const DEFAULT_CONTROLLER_NAMESPACE = "Framework\Console";
    
    public function run()
    {
        ErrorHandler::setHtmlFormatFlag(false);
        $config = $this->getConfig();
        //route
        $routeName = $config->getConfig("console_route", self::DEFAULT_ROUTE);
        $routeModel = $this->getObjectManager()->getComponent('Router', $routeName);
        $this->setRouter($routeModel);

        $request = $routeModel->dispatch();        
        $controller = $this->getObjectManager()->getComponent('Console', $request['controller']);
        if($controller) {
            $action = $request['action'];
            $controller->callActionFlow($request['action'], $request['param']);
        } else {
            throw new Exception("invalid console application");
        }
    }
    
    public function setController ($controller)
    {
        return $this->controller = $controller;
    }

    public function getController ()
    {
        return $this->controller;
    }

    public function redirect($controller, $action = null, $param = null)
    {
        throw new Exception("can not redirect in console mode");
    }
}
