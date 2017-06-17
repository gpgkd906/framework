<?php

namespace Framework\RouteModel\Http;

use Framework\RouteModel\AbstractRouteModel;
use Framework\Config\ConfigModel;
use Exception;

class RouteModel extends AbstractRouteModel
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
            if(!empty($_GET)) {
                $this->request_param = array_merge($_GET, $this->request_param);
            }
            break;
        case self::PUT:
        case self::DELETE:
            parse_str(file_get_contents('php://input'), $this->request_param);
            if(!empty($_GET)) {
                $this->request_param = array_merge($_GET, $this->request_param);
            }
            break;
        }
        return $this->request_param;
    }

    public function getReq()
    {
        return $_SERVER['REQUEST_URI'];
    }

    public function redirect($controller, $action = 'index', $param = null)
    {
        var_dump($controller);
    }

    public function parseRequest()
    {
        //[controller, action, param]
        $controller = $action = $param = null;
        $req = $this->getReq();
        if ($req[0] === '/') {
            $req = substr($req, 1);
        }
        if (strpos($req, ".")) {
            return [null, null, null, null];
        }
        if (substr($req, -1, 1) === "/") {
            $req .= $this->getIndex();
        }
        if ($req === '') {
            $req = $this->getIndex() . '/' . $this->getIndex();
        }
        $reqs = explode("/", $req);
        $parts = [];
        foreach($reqs as $idx => $token) {
            //数字で始まる文字列は名前空間やクラス名やメソッド名になり得ないのでパラメタに退避させる
            if(is_numeric($token[0])) {
                $parts[] = $this->getIndex();
                break;
            }
            $parts[] = $token;
            unset($reqs[$idx]);
        }
        $req = join('/', $parts);
        $action = self::INDEX;
        $param = array_values($reqs);
        $request = [
            'controller' => null,
            'action'     => $action,
            'param'      => $param,
            'req'        => $req,
        ];
        return $request;
    }

    public function isFaviconRequest()
    {
        return $_SERVER["REQUEST_URI"] === "/favicon.ico";
    }
}
