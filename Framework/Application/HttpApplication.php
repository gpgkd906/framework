<?php

namespace Framework\Application;

use Framework\Core\ErrorHandler;
use Framework\ViewModel\ViewModel\ViewModelManager;
use Framework\Event\EventManager\EventManager;
use Exception;

class HttpApplication extends AbstractApplication
{
    const DEFAULT_ROUTE = "Http";
    
    public function run()
    {
        $config = $this->getConfig();
        $routeName = $config->getConfig("route", self::DEFAULT_ROUTE);
        $routeModel = $this->getServiceManager()->getComponent('RouteModel', $routeName);
        $this->setRouteModel($routeModel);
        if($routeModel->isFaviconRequest()) {
            $routeModel->sendDummyFavicon();
        }

        $viewModelNamespace = $this->getServiceManager()->getServiceNamespace('ViewModel');
        ViewModelManager::setNamespace($config->getConfig("viewModelNamespace", $viewModelNamespace));
        ViewModelManager::setTemplateDir($config->getConfig("templateDir", ROOT_DIR . str_replace('\\', '/', $viewModelNamespace)));

        $request = $routeModel->dispatch();
        $controller = $this->getServiceManager()->getComponent('Controller', $request['controller']);
        $controller->callActionFlow($request['action'], $request['param']);
    }

    public function getController($controller)
    {
        $config = $this->getConfig();
        $controllerNamespace = $config->getConfig("controllerNamespace", self::DEFAULT_CONTROLLER_NAMESPACE);
        $controller = ucfirst($controller) . "Controller";
        $controllerLabel = $controllerNamespace . "\\" . $controller;
        if(!class_exists($controllerLabel)) {
            throw new Exception(sprintf(self::ERROR_INVALID_CONTROLLER_LABEL, $controllerLabel));
        }
        return $controllerLabel::getSingleton();
    }
}
