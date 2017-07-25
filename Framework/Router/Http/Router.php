<?php

namespace Framework\Router\Http;

use Framework\Router\AbstractRouter;
use Framework\Config\ConfigModel;
use Exception;

class Router extends AbstractRouter
{
    const ERROR_INVALID_LINKTO = "invalid_linkto: %s";

    const GET = "get";
    const POST = "post";
    const PUT = "put";
    const DELETE = "delete";

    private $request_method = null;
    private $request_param = [];

    private function getMethod()
    {
        if ($this->request_method === null) {
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
        switch ($this->getMethod()) {
            case self::GET:
                $this->request_param = $_GET;
                break;
            case self::POST:
                $this->request_param = $_POST;
                if (!empty($_GET)) {
                    $this->request_param = array_merge($_GET, $this->request_param);
                }
                break;
            case self::PUT:
            case self::DELETE:
            default:
                parse_str(file_get_contents('php://input'), $this->request_param);
                if (!empty($_GET)) {
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

    protected function loadRouter()
    {
        foreach (glob(ROOT_DIR . 'Framework/Module/*/*/Route.php') as $routeInjection) {
            require $routeInjection;
        }
    }

    public function linkto($controller, $param = null)
    {
        $routerList = $this->getRouterList();
        $uri = array_search($controller, $routerList);
        if ($uri) {
            $param = (array) $param;
            if (!empty($param)) {
                $uri .= '/' . join('/', $param);
                $uri = str_replace('//', '/', $uri);
            }
            $uri = '/' . $uri;
            return $uri;
        } else {
            throw new \Excpetion (sprintf(self::ERROR_INVALID_LINKTO, $controller));
        }
    }

    public function redirect($controller, $param = null)
    {
        $uri = $this->linkto($controller, $param);
        header('Location: ' . $uri, true, 301);
    }

    public function parseRequest()
    {
        $controller = $action = $param = null;
        $req = $this->getReq();
        if ($req[0] === '/') {
            $req = substr($req, 1);
        }
        if (strpos($req, ".")) {
            return [null, null, null];
        }
        if (strpos($req, "?") !== false) {
            list($req) = explode('?', $req);
        }
        if ($req === '') {
            $req = $this->getIndex();
        }
        if (substr($req, -1, 1) === "/") {
            $req = substr($req, 0, -1);
        }
        $reqs = explode("/", $req);
        $parts = [];
        foreach ($reqs as $idx => $token) {
            //数字で始まる文字列は名前空間やクラス名やメソッド名になり得ないので以降のパラメタを退避させる
            if (isset($token[0]) && is_numeric($token[0])) {
                break;
            }
            $parts[] = $token;
            unset($reqs[$idx]);
        }
        $req = join('/', $parts);
        $action = self::INDEX;
        $param = array_values($reqs);
        $request = [
            'controller' => $req,
            'action'     => $action,
            'param'      => $param,
        ];
        return $request;
    }

    public function isFaviconRequest()
    {
        return $_SERVER["REQUEST_URI"] === "/favicon.ico";
    }

    public function getRequestUri()
    {
        return $_SERVER['REQUEST_URI'];
    }
}
