<?php
/**
 * PHP version 7
 * File Router.php
 * 
 * @category Router
 * @package  Framework\Router
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
declare(strict_types=1);

namespace Framework\Router\Http;

use Framework\Router\AbstractRouter;
use Framework\Config\ConfigModel;
use Exception;

/**
 * Interface Router
 * 
 * @category Interface
 * @package  Framework\Router
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
class Router extends AbstractRouter
{
    const ERROR_INVALID_LINKTO = "invalid_linkto: %s";

    const GET = "get";
    const POST = "post";
    const PUT = "put";
    const DELETE = "delete";

    private $_request_method = null;
    private $_request_param = [];

    /**
     * Method getMethod
     *
     * @return string $request_method
     */
    private function getMethod()
    {
        if ($this->_request_method === null) {
            $request_method = isset($_REQUEST["REQUEST_METHOD"]) ? $_REQUEST["REQUEST_METHOD"] : (isset($_SERVER["REQUEST_METHOD"]) ? $_SERVER["REQUEST_METHOD"] : self::GET);
            $this->_request_method = strtolower($request_method);
        }
        return $this->_request_method;
    }

    /**
     * Method getAction
     *
     * @return string $action
     */
    public function getAction()
    {
        $request = $this->dispatch();
        $action = $request["action"];
        return $action;
    }

    /**
     * Method getController
     *
     * @return string $controller
     */
    public function getController()
    {
        $request = $this->dispatch();
        return $request['controller'];
    }

    /**
     * Method getParam
     *
     * @return array $request_param
     */
    public function getParam()
    {
        switch ($this->getMethod()) {
            case self::GET:
                $this->_request_param = $_GET;
                break;
            case self::POST:
                $this->_request_param = $_POST;
                if (!empty($_GET)) {
                    $this->_request_param = array_merge($_GET, $this->_request_param);
                }
                break;
            case self::PUT:
            case self::DELETE:
            default:
                parse_str(file_get_contents('php://input'), $this->_request_param);
                if (!empty($_GET)) {
                    $this->_request_param = array_merge($_GET, $this->_request_param);
                }
                break;
        }
        return $this->_request_param;
    }

    /**
     * Method getReq
     *
     * @return string $request_uri
     */
    public function getReq()
    {
        return $_SERVER['REQUEST_URI'];
    }

    /**
     * Method loadRouter
     *
     * @return void
     */
    protected function loadRouter()
    {
        foreach (glob(ROOT_DIR . 'Framework/Module/*/*/Route.php') as $routeInjection) {
            include $routeInjection;
        }
    }

    /**
     * Method linkto
     *
     * @param string $controller ControllerClass
     * @param mixed  $param      Param
     * 
     * @return string url
     */
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

    /**
     * Method redirect
     *
     * @param string $controller ControllerClass
     * @param mixed  $param      Param
     * 
     * @return void
     */
    public function redirect($controller, $param = null)
    {
        $uri = $this->linkto($controller, $param);
        header('Location: ' . $uri, true, 301);
    }

    /**
     * Method reload
     *
     * @return void
     */
    public function reload()
    {
        $request = $this->dispatch();
        $this->redirect($request['controller'], $request['param']);
    }

    /**
     * Method parseRequest
     *
     * @return array $request
     */
    public function parseRequest()
    {
        $controller = $action = $param = null;
        $req = $this->getReq();
        if ($req[0] === '/') {
            $req = substr($req, 1);
        }
        if (strpos($req, ".")) {
            return [
                'controller' => null, 
                'action' => null, 
                'param' => null
            ];
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

    /**
     * Method isFaviconRequest
     *
     * @return boolean
     */
    public function isFaviconRequest()
    {
        return $_SERVER["REQUEST_URI"] === "/favicon.ico";
    }

    /**
     * Method getRequestUri
     *
     * @return string $request_uri
     */
    public function getRequestUri()
    {
        return $_SERVER['REQUEST_URI'];
    }
}
