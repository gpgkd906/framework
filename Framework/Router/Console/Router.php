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

namespace Framework\Router\Console;

use Framework\Router\AbstractRouter;
use Framework\Config\ConfigModel;
use Exception;

/**
 * Class Router
 *
 * @category Class
 * @package  Framework\Router
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
class Router extends AbstractRouter
{
    /**
     * Method loadRouter
     *
     * @return void
     */
    public function loadRouter()
    {
        $request = \Zend\Diactoros\ServerRequestFactory::fromGlobals(
            $_SERVER,
            $_GET,
            $_POST,
            $_COOKIE,
            $_FILES
        );
        var_Dump($request);die;
        foreach (glob(ROOT_DIR . 'Framework/Module/*/*/Command.php') as $routeInjection) {
            include $routeInjection;
        }
    }

    /**
     * Method getParam
     *
     * @return array $argv
     */
    public function getParam()
    {
        if ($this->request_param === null) {
            global $argv;
            array_shift($argv);
            $this->setParam($argv);
        }
        return $this->request_param;
    }

    /**
     * Method parseRequest
     *
     * @return array $request
     */
    public function parseRequest()
    {
        //[action, param]
        $request = [
            'controller' => null,
            'action' => self::INDEX,
            'param' => []
        ];
        $tmp = $this->getParam();
        foreach ($tmp as $index => $arg) {
            if (strpos($arg, "=")) {
                // list($name, $val) = explode("=", $arg);
                $argTmp = explode("=", $arg);
                $name = array_shift($argTmp);
                $val = join("=", $argTmp);
                $request['param'][$name] = $val;
                unset($tmp[$index]);
            }
        }
        $request['controller'] = array_shift($tmp);
        if (!empty($tmp)) {
            $request['param'] = array_merge($request['param'], $tmp);
        }
        return $request;
    }

    /**
     * Method redirect
     *
     * @param string      $controller ControllerClass
     * @param string|null $action     Action
     * @param mixed       $param      Param
     *
     * @return void
     */
    public function redirect($controller, $action = null, $param = null)
    {
        throw new Exception("can not redirct in console mode");
    }
}
