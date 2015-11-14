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
     * @var mixed $restAction 
     * @access private
     * @link
     */
    private $restAction = null;

    /**
     * 
     * @api
     * @param mixed $restAction
     * @return mixed $restAction
     * @link
     */
    public function setRestAction ($restAction)
    {
        return $this->restAction = $restAction;
    }

    /**
     * 
     * @api
     * @return mixed $restAction
     * @link
     */
    public function getRestAction ()
    {
        if($this->restAction === null) {
            $request = $this->dispatch();
            $action = $request["action"];
            $method = $this->getMethod();
            if(self::GET !== $method) {
                $action = $method . ucfirst($action);
                $this->setRestAction($action);
            } else {
                $this->setRestAction(false);
            }
        }
        return $this->restAction;
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
        return $action;
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

    public function redirect($controller, $action = 'index', $param = null)
    {
        var_dump($controller);
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
            $req .= $this->getIndex();
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
                $controller = ucfirst($_req) . '\\Index';
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
            $_req = $reqs[0];
            if($request = $this->appMapping($_req)) {
                $param = $reqs[1];
            } else {
                $_req = join("/", $reqs);
                if(!$request = $this->appMapping($_req)) {
                    $reqs = array_map('ucfirst', $reqs);
                    $action = array_pop($reqs);
                    $controller = join('\\', $reqs);
                    $param = null;
                }
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
