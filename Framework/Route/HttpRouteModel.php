<?php

namespace Framework\Route;

use Framework\Config\ConfigModel;
use Exception;

class HttpRouteModel extends AbstractRouteModel
{
    const GET = "get";
    const POST = "post";
    const PUT = "put";
    const DELETE = "delete";

    private $request_method = null;
    private $request_param = [];

    /**
     *
     * @api
     * @var mixed $baseAction 
     * @access private
     * @link
     */
    private $baseAction = null;

    /**
     * 
     * @api
     * @param mixed $baseAction
     * @return mixed $baseAction
     * @link
     */
    public function setBaseAction ($baseAction)
    {
        return $this->baseAction = $baseAction;
    }

    /**
     * 
     * @api
     * @return mixed $baseAction
     * @link
     */
    public function getBaseAction ()
    {
        return $this->baseAction;
    }

    private function getMethod()
    {
        if($this->request_method === null) {
            $request_method = isset($_REQUEST["REQUEST_METHOD"]) ? $_REQUEST["REQUEST_METHOD"] : (isset($_SERVER["REQUEST_METHOD"]) ? $_SERVER["REQUEST_METHOD"] : self::GET);
            $this->request_method = strtolower($request_method);
        }
        return $this->request_method;
    }

    public function getAction()
    {
        $request = $this->dispatch();
        $action = $request["action"];
        $method = $this->getMethod();
        if(self::GET !== $method) {
            $this->setBaseAction($action);
            return $method . ucfirst($action);
        } else {
            return $action;
        }
    }

    public function getController()
    {
        $request = $this->dispatch();
        return $request['controller'];
    }

    public function getParam()
    {
        switch($this->getMethod()) {
        case self::GET:
            $this->request_param = $_GET;
            break;
        case self::POST:
            $this->request_param = $_POST;
            break;
        case self::PUT:
        case self::DELETE:
            parse_str(file_get_contents('php://input'), $this->request_param);
            break;
        }
        return $this->request_param;
    }

    public function redirect($controller, $action, $param = null)
    {
        
    }
    
    public function parseRequest()
    {
        //[controller, action, param]
        $controller = null;
        $action = null;
        $param = null;
        $req = $this->getReq();
        if(strpos($req, ".")) {
            return [null, null, null];
        }
        if(substr($req, -1, 1) === "/") {
            $req = substr($req, 0, -1);
        }
        $reqs = explode("/", $req);
        $reqLenth = count($reqs);
        if($reqLenth > $this->getDepths()) {
            throw new Exception(self::ERROR_OVER_MAX_DEPTHS);
        }
        switch($reqLenth) {
        case 1: //sample: index
            $_req = $reqs[0];
            if(!$request = $this->appMapping($_req)) {
                $controller = ucfirst($_req);
                $action = $this->getIndex();
                $param = null;
            }
            break;
        case 2: //sample: mypage/bookmarks, product/1
            $_req = $reqs[0];
            if($request = $this->appMapping($_req)) {
                $param = $reqs[1];
            } else {
                $_req = join("/", $reqs);
                if(!$request = $this->appMapping($_req)) {
                    $controller = ucfirst($reqs[0]) . "\\" .  ucfirst($reqs[1]);
                    $action = $this->getIndex();
                    $param = null;
                }
            }
            break;
        default: //count($reqs) >= 3: product/detail/1, admin/product/list
            $mapped = false;
            for($i = 1; $i < $reqLenth; $i++) {
                list($_req, $rest) = $this->joinStep($reqs, $i);
                if($request = $this->appMapping($_req)) {
                    $param = $rest;
                    $mapped = true;
                    break;
                }
            }
            if($mapped === false) {
                $controller = ucfirst($reqs[0]) . "\\" .  ucfirst($reqs[1]);
                $action = $reqs[2];
                $param = array_slice($reqs, 3);
            }
            break;
        }
        if(empty($controller)) {
            $controller = $this->getIndex();
        }
        $request = [
            'controller' => $controller,
            'action' => $action,
            'param' => $param
        ];        
        return $request;
    }

    public function isFaviconRequest() 
    {
        return $_SERVER["REQUEST_URI"] === "/favicon.ico";
    }

    public function sendDummyFavicon()
    {
        header('Content-Type: image/vnd.microsoft.icon');
        header('Content-length: 0');
        die();
    }

    public function appMapping($req)
    {
        if(isset($this->appUrl[$req])) {
            return $this->appUrl[$req];
        }
    }
}
