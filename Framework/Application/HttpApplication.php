<?php

namespace Framework\Application;

use Framework\Core\ErrorHandler;
use Framework\ViewModel\ViewModel\ViewModelManager;
use Framework\Event\EventManager\EventTargetInterface;
use Framework\Controller\Controller\ControllerInterface;
use Exception;

class HttpApplication extends AbstractApplication implements EventTargetInterface
{
    
    use \Framework\Event\EventManager\EventTargetTrait;
    const DEFAULT_ROUTE = "Http";
    const TRIGGER_ROUTEMISS = 'routemiss';

    /**
     *
     * @api
     * @var mixed $controller 
     * @access private
     * @link
     */
    private $controller = null;
    
    public function run()
    {
        $config = $this->getConfig();
        $routeName = $config->getConfig("route", self::DEFAULT_ROUTE);
        $routeModel = $this->getServiceManager()->getComponent('RouteModel', $routeName);
        $this->setRouteModel($routeModel);
        if($routeModel->isFaviconRequest()) {
            $this->sendDummyFavicon();
        }

        $viewModelNamespace = $this->getServiceManager()->getServiceNamespace('ViewModel');
        ViewModelManager::setNamespace($config->getConfig("viewModelNamespace", $viewModelNamespace));
        ViewModelManager::setTemplateDir($config->getConfig("templateDir", ROOT_DIR . str_replace('\\', '/', $viewModelNamespace)));
        ViewModelManager::setBasePath($config->getConfig('ApplicationHost'));
        ViewModelManager::setServiceManager($this->getServiceManager());        
        $request = $routeModel->dispatch();        
        $controller = $this->getServiceManager()->getComponent('Controller', $request['controller']);
        $action = $request['action'];
        if(!$controller || !is_callable([$controller, $action])) {
            $this->triggerEvent(self::TRIGGER_ROUTEMISS, $request);
            $controller = $this->getController();
            if(!$controller instanceof ControllerInterface) {
                $this->sendNotFound();
            }
        }
        $controller->callActionFlow($request['action'], $request['param']);
    }

    public function setController ($controller)
    {
        return $this->controller = $controller;
    }

    public function getController ()
    {
        return $this->controller;
    }

    public function sendDummyFavicon()
    {
        header('Content-Type: image/vnd.microsoft.icon');
        header('Content-length: 0');
        die();
    }

    public function sendNotFound()
    {
        header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
        echo '404 Not Found';
        die;
    }   
}
