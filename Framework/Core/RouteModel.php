<?php

namespace Framework\Core;

use Framework\Core\Interfaces\RouteModelInterface;
use Framework\Config\ConfigModel;
use Exception;

class RouteModel implements RouteModelInterface
{
    const ERROR_INVALID_JOINSTEP = "error: invalid join-step";
    const ERROR_OVER_MAX_DEPTHS = "error: over max_depths";

    const GET = "get";
    const POST = "post";
    const PUT = "put";
    const DELETE = "delete";

    private $request_method = null;
    private $request_param = [];
    private $default_req = 'index';
    private $max_depths = 10;
    private $isConsole = null;

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
        $this->max_depths = $this->config->getConfig("max_depths", $this->max_depths);
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

    public function appMapping($req)
    {
        if(isset($this->appUrl[$req])) {
            return $this->appUrl[$req];
        }
    }

    public function parseRequest()
    {
        //controller, action, param
        $request = [null, null, null];
        $req = $this->getReq();
        if(strpos($req, ".")) {
            return [null, null, null];
        }
        if(substr($req, -1, 1) === "/") {
            $req = substr($req, 0, -1);
        }
        $reqs = explode("/", $req);
        $reqLenth = count($reqs);
        if($reqLenth > $this->max_depths) {
            throw new Exception(self::ERROR_OVER_MAX_DEPTHS);
        }
        switch($reqLenth) {
        case 1: //sample: index
            $_req = $reqs[0];
            if(!$request = $this->appMapping($_req)) {
                $request = [ucfirst($_req), $this->default_req, null];
            }
            break;
        case 2: //sample: mypage/bookmarks, product/1
            $_req = $reqs[0];
            if($request = $this->appMapping($_req)) {
                $request[2] = $reqs[1];
            } else {
                $_req = join("/", $reqs);
                if(!$request = $this->appMapping($_req)) {
                    $_req = ucfirst($reqs[0]) . "\\" .  ucfirst($reqs[1]);
                    $request = [$_req, $this->default_req, null];
                }
            }
            break;
        default: //count($reqs) >= 3: product/detail/1, admin/product/list
            $mapped = false;
            for($i = 1; $i < $reqLenth; $i++) {
                list($_req, $rest) = $this->joinStep($reqs, $i);
                if($request = $this->appMapping($_req)) {
                    $request[2] = $rest;
                    $mapped = true;
                    break;
                }
            }
            if($mapped === false) {
                $_req = ucfirst($reqs[0]) . "\\" .  ucfirst($reqs[1]);
                $request = [$_req, $reqs[2], array_slice($reqs, 3)];                
            }
            break;
        }
        if(empty($request[1])) {
            $request[1] = $this->default_req;
        }
        return $request;
    }

    private function joinStep($array, $step = 1, $delimiter = "/")
    {
        if(count($array) < $step) {
            throw new Exception(self::INVALID_JOINSTEP);
        }
        $joinArr = array_slice($array, 0, $step);
        $rest = array_slice($array, $step);
        return [join($delimiter, $joinArr), $rest];
    }

    public function getReq() {
        $param = $this->getParam();
        if(isset($param["req"])) {
            return $param["req"];
        } else {
            return $this->default_req;
        }
    }

    public function setIsConsole($isConsole)
    {
        $this->isConsole = $isConsole;
    }

    public function isConsole()
    {
        if($this->isConsole === null) {
            $this->isConsole = php_sapi_name() === "cli";
        }
        return $this->isConsole;
    }

    public function parseCommand()
    {
        global $argv;
        array_shift($argv);
        //controller, action, param
        $request = [null, null, null];
        $param = [];
        foreach($argv as $arg) {
            if(strpos($arg, "=")) {
                list($name, $val) = explode("=", $arg);
                $param[$name] = $val;
            } else {
                //is controller?
                if(!isset($request[0])) {
                    $request[0] = $arg;
                    //or is action?
                } else if(!isset($request[0])) {
                    $request[1] = $arg;
                    //or is parame
                } else {
                    $param[$arg] = true;
                }
            }
        }
        return $param;
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
}
