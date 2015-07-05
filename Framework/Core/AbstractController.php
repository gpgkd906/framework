<?php

namespace Framework\Core;

use Framework\Core\Interfaces\ControllerInterface;
use Framework\Core\Interfaces\ViewModelInterface;
use Framework\Core\ViewModel\AbstractViewModel;
use Framework\Core\Interfaces\EventInterface;
use Exception;

abstract class AbstractController implements ControllerInterface, EventInterface
{    
    use \Framework\Core\EventManager\EventTrait;
    
    static private $instance = [];
    
    //error
    const ERROR_INVALID_RESPONSE_TYPE = "error: invalid response-type";
    const ERROR_ACTION_RETURN_IS_NOT_VIEWMODEL = "error: return-value is not valid view model from action %s ";
    
    private $responseType = AbstractViewModel::renderAsHTML;

    private $controllerName = null;
    private $ViewModel = null;
    
    static public function getSingleton() {
        $controllerName = get_called_class();
        if(!isset(self::$instance[$controllerName])) {
            self::$instance[$controllerName] = new $controllerName();
            self::$instance[$controllerName]->setSelfName($controllerName);
        }
        return self::$instance[$controllerName];
    }
    
    public function callActionFlow($action, $param)
    {
        $routeModel = App::getRouteModel();
        $method = $routeModel->getMethod();
        if($routeModel::GET !== $method) {
            $actionMethod = $method . ucfirst($action);
        } else {
            $actionMethod = $action;
        }
        if(is_callable([$this, $actionMethod])) {
            $this->callAction("beforeAction");
            $viewModel = $this->callAction($actionMethod, $param);
            $this->setViewModel($viewModel);
            $this->callAction("afterAction");
            if(isset($viewModel)) {
                if($viewModel instanceof ViewModelInterface) {
                    $viewModel->setRenderType($this->responseType);
                    $this->callAction("beforeResponse");
                    $this->callAction("response");
                    $this->callAction("afterResponse");
                } else {
                    throw new Exception(sprintf(self::ERROR_ACTION_RETURN_IS_NOT_VIEWMODEL, $this->getSelfName() . "::" . $actionMethod));
                }
            }
        } else {
            throw new Exception("not implements for not_found");
        }
    }

    protected function callAction($action, $param = [])
    {
        if(is_callable([$this, $action])) {
            if($param === null) {
                $param = [];
            }
            return call_user_func([$this, $action], $param);
        }
    }

    public function setResponseType($responseType)
    {
        if($responseType === AbstractViewModel::renderAsHTML
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
        $this->getViewModel()->render();
    }

    public function setSelfName($controllerName)
    {
        $this->controllerName = $controllerName;
    }

    public function getSelfName()
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
}
