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
    private $_request_uri = null;

    /**
     * Method getMethod
     *
     * @return string $request_method
     */
    public function getMethod()
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
        $request = $this->getRequest();
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
        $request = $this->getRequest();
        return $request['controller'];
    }

    /**
     * Method getParam
     *
     * @return array $request_param
     */
    public function getParam()
    {
        if ($this->request_param === null) {
            $request_param = null;
            switch ($this->getMethod()) {
                case self::GET:
                    $request_param = $_GET;
                    break;
                case self::POST:
                    $request_param = $_POST;
                    if (!empty($_GET)) {
                        $request_param = array_merge($_GET, $request_param);
                    }
                    break;
                case self::PUT:
                case self::DELETE:
                default:
                    parse_str(file_get_contents('php://input'), $request_param);
                    if (!empty($_GET)) {
                        $request_param = array_merge($_GET, $request_param);
                    }
                    break;
            }
            $this->setParam($request_param);
        }
        return $this->request_param;
    }

    /**
     * Method loadRouter
     *
     * @return void
     */
    public function loadRouter()
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
            throw new Exception (sprintf(self::ERROR_INVALID_LINKTO, $controller));
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
        $request = $this->getRequest();
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
        $req = $this->getRequestUri();
        if (strpos($req, ".")) {
            return [
                'controller' => null,
                'action' => null,
                'param' => null
            ];
        }
        if ($req[0] === '/') {
            $req = substr($req, 1);
        }
        if (substr($req, -1, 1) === "/") {
            $req = substr($req, 0, -1);
        }
        if (strpos($req, "?") !== false) {
            list($req) = explode('?', $req);
        }
        if ($req === '') {
            $req = $this->getIndex();
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
        return $this->getRequestUri() === "/favicon.ico";
    }

    /**
     * Method getRequestUri
     *
     * @return string $request_uri
     */
    public function getRequestUri()
    {
        if ($this->_request_uri === null) {
            $this->setRequestUri($_SERVER['REQUEST_URI']);
        }
        return $this->_request_uri;
    }

    /**
     * Method setRequestUri
     *
     * @param string $requestUri requestUri
     *
     * @return this
     */
    public function setRequestUri($requestUri)
    {
        $this->_request_uri = $requestUri;
        return $this;
    }
}
