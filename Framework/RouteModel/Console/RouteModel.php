<?php

namespace Framework\RouteModel\ConsoleRouteModel;

use Framework\RouteModel\AbstractRouteModel;
use Framework\Config\ConfigModel;
use Exception;

class ConsoleRouteModel extends AbstractRouteModel
{
    const ERROR_INVALID_JOINSTEP = "error: invalid join-step";
    const ERROR_OVER_MAX_DEPTHS = "error: over max_depths";

    private $request_method = null;
    private $request_param = [];

    public function getParam()
    {
        global $argv;
        array_shift($argv);
        return $this->request_param = $argv;
    }

    public function parseRequest()
    {
        //[action, param]
        $param = [];
        foreach($this->getParam() as $arg) {
            if(strpos($arg, "=")) {
                list($name, $val) = explode("=", $arg);
                $param[$name] = $val;
            } else {
                //パラメタ名が設定されてなければ、controller > action > paramの順番で設定していく
                if(!isset($request[0])) {
                    $controller = $arg;
                 } else if(!isset($request[0])) {
                    $action = $arg;
                } else {
                    $param[$arg] = true;
                }
            }
        }
        if(empty($action)) {
            $action = 'index';
        }
        $request = [
            'controller' => null,
            'action' => $action,
            'param' => $param
        ];
        return $request;
    }

    public function redirect($controller, $action = null, $param = null)
    {
        throw new Exception("can not redirct in console mode");
    }
}
