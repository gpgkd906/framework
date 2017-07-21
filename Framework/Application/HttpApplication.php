<?php

namespace Framework\Application;

use Framework\ViewModel\ViewModelManager;
use Framework\EventManager\EventTargetInterface;
use Framework\Controller\ControllerInterface;
use Framework\Router\RouterInterface;
use Framework\Router\Http\Router;
use Exception;

class HttpApplication extends AbstractApplication implements EventTargetInterface
{

    use \Framework\EventManager\EventTargetTrait;
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
        $routeModel = $this->getObjectManager()->get(RouterInterface::class, Router::class);
        if ($routeModel->isFaviconRequest()) {
            $this->sendDummyFavicon();
        }

        ViewModelManager::setBasePath($config->get('ApplicationHost'));
        ViewModelManager::setObjectManager($this->getObjectManager());
        $request = $routeModel->dispatch();
        $Controller = null;
        if ($request['controller']) {
            $Controller = $this->getObjectManager()->get(ControllerInterface::class, $request['controller']);
            $action = $request['action'];
            if (!$Controller || !is_callable([$Controller, $action])) {
                $this->triggerEvent(self::TRIGGER_ROUTEMISS, $request);
                $Controller = $this->getController();
            }
        }
        if (!$Controller instanceof ControllerInterface) {
            return $this->sendNotFound();
        }
        $Controller->callActionFlow($request['action'], $request['param']);
    }

    public function setController($controller)
    {
        return $this->controller = $controller;
    }

    public function getController()
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
    }
}
