<?php

namespace Framework\Application;

use Framework\Log\ErrorHandler;
use Framework\Router\RouterInterface;
use Framework\Router\Console\Router;
use Framework\Controller\ControllerInterface;
use Exception;

class ConsoleApplication extends AbstractApplication
{
    const DEFAULT_ROUTE = "Console";
    const DEFAULT_CONTROLLER_NAMESPACE = "Framework\Console";

    public function run()
    {
        ErrorHandler::setHtmlFormatFlag(false);
        $config = $this->getConfig();
        //route
        $routeModel = $this->getObjectManager()->get(RouterInterface::class, Router::class);

        $request = $routeModel->dispatch();
        $Controller = $this->getObjectManager()->get(ControllerInterface::class, $request['controller']);
        if ($Controller) {
            $action = $request['action'];
            $Controller->callActionFlow($request['action'], $request['param']);
        } else {
            throw new Exception("invalid console application");
        }
    }

    public function setController($Controller)
    {
        return $this->controller = $Controller;
    }

    public function getController()
    {
        return $this->controller;
    }
}
