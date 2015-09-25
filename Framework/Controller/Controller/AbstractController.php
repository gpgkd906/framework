<?php

namespace Framework\Controller\Controller;

use Framework\Application\HttpApplication;
use Framework\ViewModel\ViewModel\ViewModelInterface;
use Framework\ViewModel\ViewModel\AbstractViewModel;
use Framework\Event\Event\EventInterface;
use Exception;

abstract class AbstractController implements ControllerInterface, EventInterface
{    
    use \Framework\Event\Event\EventTrait;
    
    static private $instance = [];
    
    //error
    const ERROR_INVALID_RESPONSE_TYPE = "error: invalid response-type";
    const ERROR_ACTION_RETURN_IS_NOT_VIEWMODEL = "error: return-value is not valid view model from action %s ";
    
    private $responseType = AbstractViewModel::renderAsHTML;

    private $controllerName = null;
    private $ViewModel = null;

        /**
     *
     * @api
     * @var mixed $serviceManager 
     * @access private
     * @link
     */
    private $serviceManager = null;

    /**
     * 
     * @api
     * @param mixed $serviceManager
     * @return mixed $serviceManager
     * @link
     */
    public function setServiceManager ($serviceManager)
    {
        return $this->serviceManager = $serviceManager;
    }

    /**
     * 
     * @api
     * @return mixed $serviceManager
     * @link
     */
    public function getServiceManager ()
    {
        return $this->serviceManager;
    }
    
    static public function getSingleton() {
        $controllerName = static::class;
        if(!isset(self::$instance[$controllerName])) {
            self::$instance[$controllerName] = new $controllerName();
            self::$instance[$controllerName]->setSelfName($controllerName);
        }
        return self::$instance[$controllerName];
    }
    
    public function callActionFlow($action, $param)
    {
        $routeModel = $this->getServiceManager()->getApplication()->getRouteModel();
        $actionMethod = $routeModel->getAction();
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
