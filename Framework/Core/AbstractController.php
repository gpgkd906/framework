<?php

namespace Framework\Core;

use Framework\Core\Interfaces\ControllerInterface;
use Exception;

abstract class AbstractController implements ControllerInterface
{    
    static private $instance = [];
    
    const responseHTML = "html";
    const responseJSON = "json";
    const responseXML = "xml";
    //error
    const ERROR_INVALID_RESPONSE_TYPE = "error: invalid response-type";
    
    private $responseType = "html";

    static public function getSingleton() {
        $controllerName = get_called_class();
        if(!isset(self::$instance[$controllerName])) {
            self::$instance[$controllerName] = new $controllerName();
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
            $this->callAction($actionMethod, $param);
            $this->callAction("afterAction");
            $this->callAction("beforeResponse");
            $this->callAction("response");
            $this->callAction("afterResponse");
        } else {
            throw new Exception("not implements for not_found");
        }
    }

    protected function callAction($action, $param = null)
    {
        if(is_callable([$this, $action])) {
            $this->{$action}($param);
        }
    }

    public function setResponseType($responseType)
    {
        if($responseType === self::responseHTML
        || $responseType === self::responseJSON
        || $responseType === self::responseXML
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
        switch($this->responseType) {
        case self::responseHTML:
            $this->responseHTML();
            break;
        case self::responseJSON:
            $this->responseJSON();
            break;
        case self::responseXML:
            $this->responseXML();
            break;
        default:
            throw new Exception(self::ERROR_INVALID_RESPONSE_TYPE);
            break;
        }
    }
    
    protected function responseHtml()
    {

    }

    protected function responseJSON()
    {

    }

    protected function responseXML()
    {
        
    }
}
