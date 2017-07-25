<?php

namespace Framework\Router\Console;

use Framework\Router\AbstractRouter;
use Framework\Config\ConfigModel;
use Exception;

class Router extends AbstractRouter
{
    private $request_param = [];

    protected function loadRouter()
    {
        foreach (glob(ROOT_DIR . 'Framework/Module/*/*/Command.php') as $routeInjection) {
            require $routeInjection;
        }
    }

    public function getParam()
    {
        global $argv;
        array_shift($argv);
        return $this->request_param = $argv;
    }

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

    public function redirect($controller, $action = null, $param = null)
    {
        throw new Exception("can not redirct in console mode");
    }
}
