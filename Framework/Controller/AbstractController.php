<?php

namespace Framework\Controller;

use Framework\Application\HttpApplication;
use Framework\ObjectManager\ObjectManagerAwareInterface;
use Framework\ViewModel\ViewModel\ViewModelInterface;
use Framework\ViewModel\ViewModel\AbstractViewModel;
use Framework\EventManager\EventTargetInterface;
use Framework\Router\RouterAwareInterface;
use Framework\Service\SessionService\SessionServiceAwareInterface;
use Exception;

abstract class AbstractController
    implements ControllerInterface, EventTargetInterface,
                ObjectManagerAwareInterface, RouterAwareInterface, SessionServiceAwareInterface
{
    use \Framework\EventManager\EventTargetTrait;
    use \Framework\ObjectManager\ObjectManagerAwareTrait;
    use \Framework\Router\RouterAwareTrait;
    use \Framework\Service\SessionService\SessionServiceAwareTrait;

    static private $instance = [];

    //error
    const ERROR_INVALID_RESPONSE_TYPE = "error: invalid response-type";
    const ERROR_ACTION_RETURN_IS_NOT_VIEWMODEL = "error: return-value is not valid view model from action %s ";
    const ERROR_INVALID_CONTROLLER_FOR_EXCHANGE = 'error: invalid-controller:[%s] for exchange';

    //EVENT
    const TRIGGER_BEFORE_ACTION = 'beforeAction';
    const TRIGGER_AFTER_ACTION = 'afterAction';
    const TRIGGER_BEFORE_RESPONSE = 'beforeResponse';
    const TRIGGER_AFTER_RESPONSE = 'afterResponse';

    private $responseType = AbstractViewModel::renderAsHTML;

    private $controllerName = null;
    private $ViewModel = null;

    static public function getSingleton() {
        $controllerName = static::class;
        if (!isset(self::$instance[$controllerName])) {
            self::$instance[$controllerName] = new $controllerName();
            self::$instance[$controllerName]->setName($controllerName);
        }
        return self::$instance[$controllerName];
    }

    public function callActionFlow($action, $param)
    {
        $routeModel = $this->getRouter();
        $restActionMethod = $routeModel->getRestAction();
        $actionMethod = $routeModel->getAction();
        if ($restActionMethod && !is_callable([$this, $restActionMethod])) {
            $restActionMethod = false;
        }
        if (!is_callable([$this, $actionMethod])) {
            $actionMethod = false;
        }
        if ($actionMethod === false && $restActionMethod === false) {
            return $this->getObjectManager()->getApplication()->sendNotFound();
        }
        $this->triggerEvent(self::TRIGGER_BEFORE_ACTION);
        if ($restActionMethod) {
            $this->callAction($restActionMethod, $param);
        }
        if ($actionMethod) {
            if ($viewModel = $this->callAction($actionMethod, $param)) {
                $this->setViewModel($viewModel);
            }
        }
        $this->triggerEvent(self::TRIGGER_AFTER_ACTION);
        $viewModel = $this->getViewModel();
        if (isset($viewModel)) {
            if ($viewModel instanceof ViewModelInterface) {
                $viewModel->setRenderType($this->responseType);
                $this->triggerEvent(self::TRIGGER_BEFORE_RESPONSE);
                $this->callAction("response");
                $this->triggerEvent(self::TRIGGER_AFTER_RESPONSE);
            } else {
                throw new Exception(sprintf(self::ERROR_ACTION_RETURN_IS_NOT_VIEWMODEL, $this->getName() . "::" . $actionMethod));
            }
        }
    }

    protected function callAction($action, $param = [])
    {
        if (is_callable([$this, $action])) {
            if ($param === null) {
                $param = [];
            }
            return call_user_func([$this, $action], $param);
        }
    }

    public function setResponseType($responseType)
    {
        if ($responseType === AbstractViewModel::renderAsHTML
        || $responseType === AbstractViewModel::renderAsJSON
        || $responseType === AbstractViewModel::renderAsXML
        ) {
            $this->responseType = $responseType;
        } else {
            throw new Exception(self::ERROR_INVALID_RESPONSE_TYPE);
        }
    }

    public function getResponseType()
    {
        return $this->responseType;
    }

    public function response()
    {
        $this->getSessionService()->close();
        echo $this->getViewModel()->render();
    }

    public function setName($controllerName)
    {
        $this->controllerName = $controllerName;
    }

    public function getName()
    {
        return $this->controllerName;
    }

    public function setViewModel($ViewModel)
    {
        $this->ViewModel = $ViewModel;
    }

    public function getViewModel()
    {
        return $this->ViewModel;
    }

    public function getParam()
    {
        $param = $this->getRouter()->getParam();
        unset($param['req']);
        return $param;
    }

    public function exChange($targetController, $action = 'index', $param = null)
    {
        if (!is_subclass_of($targetController, __CLASS__)) {
            throw new Exception(sprintf(self::ERROR_INVALID_CONTROLLER_FOR_EXCHANGE, $targetController));
        }
        $Controller = $targetController::getSingleton();
        $ViewModel = $Controller->callAction($action, $param);
        $this->setViewModel($ViewModel);
    }
}
