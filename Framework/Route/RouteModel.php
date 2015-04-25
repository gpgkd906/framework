<?php

namespace Framework\Route;

use Framework\Core\Interfaces\RouteModelInterface;
use Framework\Config\ConfigModel;

class RouteModel implements RouteModelInterface
{

    const GET = "get";
    const POST = "post";
    const PUT = "put";
    const DELETE = "delete";

    private $request_method = null;
    private $request_param = [];
    private $default_req = 'index';

    static private $instance = null;

    private $config = null;
    private $appUrl = [];

    private function __construct() {
        $this->config = ConfigModel::getConfigModel([
            "scope" => ConfigModel::ROUTE,
            "property" => ConfigModel::READONLY
        ]);
        $this->appUrl = $this->config->getConfig("appUrl");
        $this->default_req = $this->config->getConfig("default_req", $this->default_req);
    }
    
    public static function getSingleton()
    {
        if(self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;        
    }
    
    public function dispatch()
    {
        if($this->isConsole()) {
            $request = $this->parseCommand();
        } else if($request = $this->appMapping()) {
            //nothing do here
        } else {
            $request = $this->parseRequest();
        }
        return $request;
    }
    
    public function getMethod()
    {
        if($this->request_method === null) {
            $request_method = isset($_REQUEST["REQUEST_METHOD"]) ? $_REQUEST["REQUEST_METHOD"] : (isset($_SERVER["REQUEST_METHOD"]) ? $_SERVER["REQUEST_METHOD"] : self::GET);
            $this->request_method = strtolower($request_method);
        }
        return $this->request_method;
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
    
    public function update()
    {
        
    }

    public function refresh()
    {
        
    }

    public function appMapping()
    {
        $req = $this->getReq();
        if(isset($this->appUrl[$req])) {
            return $this->appUrl[$req];
        }
    }

    public function parseRequest()
    {
        $req = $this->getReq();
        if(strpos($req, ".")) {
            return [null, null, null];
        }
        if(substr($req, -1, 1) === "/") {
            $req = substr($req, 0, -1);
        }
        $reqs = explode("/", $req);
        //controllerName
        if(empty($reqs[0])) {
            $reqs[0] = "index";
        }
        //actionName
        if(!isset($reqs[1])) {
            $reqs[] = "index";
        }
        if(!isset($reqs[2])) {
            $reqs[] = null;
        }
        return $reqs;
    }

    public function getReq() {
        $param = $this->getParam();
        if(isset($param["req"])) {
            return $param["req"];
        } else {
            return $this->default_req;
        }
    }

    public function isConsole()
    {
        return php_sapi_name() === "cli";
    }

    public function parseCommand()
    {
        global $argv;
        array_shift($argv);
        $param = [];
        foreach($argv as $arg) {
            list($name, $val) = explode("=", $arg);
            $param[$name] = $val;
        }
        return $param;
    }
}
