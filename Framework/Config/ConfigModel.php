<?php

namespace Framework\Config;

use Framework\Core\App;

use Framework\Core\Interfaces\ConfigModelInterface;

class ConfigModel implements ConfigModelInterface {
    
    //scope
    const SUPER = "global";
    const ROUTE = "route";
    //property
    const READONLY = 10;
    const READWRITE = 11;
    //
    const ERROR_READONLY = "error: readonly";
    const ERROR_INVALID = "error: invalid config";

    static private $namespace = null;
    
    static private $instances = [];
    
    static private $dir = null;
    
    private $scope = null;
    private $config = [];
    private $property;
    
    static public function registerNamespace($namespace)
    {
        return self::$namespace = $namespace;
    }

    static public function getConfigModel($config)
    {
        $namespace = self::$namespace;
        $scope = $config["scope"];
        $property = $config["property"];
        $configName = join(".", [$namespace, $scope, $property]);
        if(!isset(self::$instances[$configName])) {
            $configFile = self::getFile($namespace, $scope);
            if(is_file($configFile)) {
                $config = require($configFile);
            } else {
                $config = [];
            }
            self::$instances[$configName] = new self($scope, $config, $property);
        }
        return self::$instances[$configName];
    }

    private function __construct($scope, $config, $property = null)
    {
        if($property === null) {
            $property = self::READONLY;
        }
        $this->scope = $scope;
        $this->config = $config;
        $this->property = $property;
    }

    static private function getFile($namespace, $configName)
    {
        if(self::$dir === null) {
            self::$dir = ROOT_DIR . "Framework/Config/" . $namespace . "/";
        }
        return self::$dir . $configName . ".php";
    }

    public function getScope()
    {
        return $this->scope;
    }

    public function getConfig($key, $default = null)
    {
        if(isset($this->config[$key])) {
            return $this->config[$key];
        }
        return $default;
    }

    public function setConfig($key, $value)
    {
        if($this->property === self::READONLY) {
            throw new Exception(self::ERROR_READONLY);
        }
    }

    public function update()
    {
        if($this->property === self::READONLY) {
            throw new Exception(self::ERROR_READONLY);
        }
        
    }

    public function refresh()
    {

    }
}
