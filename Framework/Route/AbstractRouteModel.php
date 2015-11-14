<?php

namespace Framework\Route;

use Framework\Application\ServiceManagerAwareInterface;
use Framework\Route\RouteModelInterface;
use Framework\Config\ConfigModel;
use Exception;

abstract class AbstractRouteModel implements RouteModelInterface, ServiceManagerAwareInterface
{
    use \Framework\Application\ServiceManagerAwareTrait;
    
    const ERROR_INVALID_JOINSTEP = "error: invalid join-step";
    const ERROR_OVER_MAX_DEPTHS = "error: over max_depths";

    private $index = 'index';
    private $depths = 10;

    static private $instances = [];

    private $config = null;
    private $appUrl = [];
    private $request = [];
    
    private function __construct() {
        $this->config = ConfigModel::getConfigModel([
            "scope" => static::class,
            "property" => ConfigModel::READONLY
        ]);
        $this->appUrl = $this->config->getConfig("appUrl", []);
        $this->index = $this->config->getConfig("index", $this->index);
        $this->depths = $this->config->getConfig("max_depths", $this->depths);
    }
    
    public static function getSingleton()
    {
        $className = static::class;
        if(!isset(self::$instances[$className])) {
            self::$instances[$className] = new $className();
        }
        return self::$instances[$className];        
    }
    
    public function dispatch()
    {
        if(empty($this->request)) {
            $this->request = $this->parseRequest();
        }
        return $this->request;            
    }

    public function getController() {}

    public function getAction() {}

    abstract public function getParam();

    abstract public function redirect($controller, $action, $param = null);
    
    public function update() {}

    public function refresh() {}

    abstract public function parseRequest();

    protected function joinStep($array, $step = 1, $delimiter = "/")
    {
        if(count($array) < $step) {
            throw new Exception(self::INVALID_JOINSTEP);
        }
        $joinArr = array_slice($array, 0, $step);
        $rest = array_slice($array, $step);
        return [join($delimiter, $joinArr), $rest];
    }

    public function getReq()
    {
        $param = $this->getParam();
        if(isset($param["req"])) {
            return $param["req"];
        } else {
            if(isset($_GET['req'])) {
                return $_GET['req'];
            }
            return $this->index;
        }
    }

    public function getindex()
    {
        return $this->index;
    }

    public function getDepths()
    {
        return $this->depths;
    }

    public function setConfig ($config)
    {
        return $this->config = $config;
    }

    public function getConfig ()
    {
        return $this->config;
    }   
}
