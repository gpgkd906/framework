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
    private $_request_param = [];

    /**
     * Method loadRouter
     *
     * @return void
     */
    protected function loadRouter()
    {
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
        global $argv;
        array_shift($argv);
        return $this->_request_param = $argv;
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
                if ($name == 'controller') {
                    $request['controller'] = $val;
                } else {
                    $request['param'][$name] = $val;
                }
                unset($tmp[$index]);
            }
        }
        if (!$request['controller']) {
            $request['controller'] = array_shift($tmp);
        }
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
