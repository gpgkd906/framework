<?php

namespace Framework\Config\ConfigModel;

use Framework\Event\Event\EventInterface;
use Exception;

class AbstractConfigModel implements ConfigModelInterface 
{
    //scope
    const SUPER = "Global";
    const ROUTE = "Route";
    const MODEL = "Model";
    const VIEW = "View";
    const PLUGINS = "Plugins";
    //property
    const READONLY  = 10;
    const READWRITE = 11;
    //
    const TYPE_PHP_ARRAY = 100;
    const TYPE_INI_FILE  = 101;
    const TYPE_JSON_FILE = 102;
    const TYPE_CALLBACK  = 103;
    //
    const ERROR_READONLY = "error: readonly";
    const ERROR_INVALID = "error: invalid config";
    const ERROR_INVALID_SCOPE_OR_FILEPATH = "error: invalid scope or config-filepath";
    const ERROR_INVALID_CALLBACK = "error: invalid callback";
    const ERROR_INVALID_INI_CONFIG = "error: invaliad ini config: %s";
    const ERROR_INVALID_JSON_CONFIG = "error: invalid json config: %s";

    static private $namespace = null;
    
    static private $instances = [];
    
    static protected $dir = null;
    
    private $scope = null;
    private $config = [];
    private $property;
    private $type = null;

    static public function registerNamespace($namespace)
    {
        return self::$namespace = $namespace;
    }

    static public function getConfigModel($metaConfig)
    {
        $namespace = self::$namespace;
        $scope = $metaConfig["scope"];
        if(isset($metaConfig["property"])) {
            $property = $metaConfig["property"];
        } else {
            $property = self::READONLY;
        }
        if(isset($metaConfig['type'])) {
            $type = $metaConfig['type'];
        } else {
            $type = self::TYPE_PHP_ARRAY;
        }
        $configName = join(".", [$namespace, $scope, $property]);
        if(!isset(self::$instances[$configName])) {
            $config = self::loadConfig($metaConfig, $type);
            self::$instances[$configName] = new self($scope, $config, $property, $type);
        }
        return self::$instances[$configName];
    }

    static public function loadConfig($metaConfig, $type)
    {
        if($type === self::TYPE_CALLBACK) {
            $config = self::loadConfigFromCallback($metaConfig);
        } else {
            $config = self::loadConfigFromFile($metaConfig, $type);
        }
        return $config;
    }
    
    static public function loadConfigFromFile($metaConfig, $type)
    {
        if(!isset($metaConfig['scope']) && !isset($metaConfig['configFile'])) {
            throw new Exception(self::INVALID_SCOPE_OR_FILEPATH);
        }
        if(isset($metaConfig['configFile'])) {
            $configFile = $metaConfig['configFile'];
        } else {
            if(isset($metaConfig['namespace'])) {
                $namespace = $metaConfig['namespace'];
            } else {
                $namespace = self::$namespace;
            }
            $configFile = static::getFile($namespace, $metaConfig['scope']);
        }
        if(is_file($configFile)) {
            switch($type) {
            case self::TYPE_PHP_ARRAY:
                $config = require($configFile);
                break;
            case self::TYPE_INI_FILE:
                if(!$config = parse_ini_file($configFile, true)) {
                    
                }
                break;
            case self::TYPE_JSON_FILE:
                $contents = file_get_contents($configFile);
                if(!$config = json_decode($contents, true)) {
                }
                break;
            }
        } else {
            $config = [];
        }
        return $config;
    }

    static public function loadConfigFromCallback($metaConfig)
    {
        if(!isset($metaConfig['callback'])) {
            throw new Exception(self::ERROR_INVALID_CALLBACK);
        }
        if(!isset($metaConfig['callback']['loadConfig'])) {
            throw new Exception(self::ERROR_INVALID_CALLBACK);
        }
        if(!is_callable($metaConfig['callback']['loadConfig'])) {
            throw new Exception(self::ERROR_INVALID_CALLBACK);
        }
        return call_use_func($metaConfig['callback']['loadConfig']);
    }

    private function __construct($scope, $config, $property, $type)
    {
        $this->scope = $scope;
        $this->config = $config;
        $this->property = $property;
        $this->type = $type;
    }

    static protected function getFile($namespace, $configName)
    {
        //need to be override
        throw new Exception("you must override method[getFile]");
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
        if(isset($this->config[$key])) {
            $this->config[$key] = $value;
        }
        return $value;
    }

    public function update()
    {
        if($this->property === self::READONLY) {
            throw new Exception(self::ERROR_READONLY);
        }
        
    }

    private function updateToFile()
    {
        
    }

    private function updateByCallBack()
    {
        
    }

    public function refresh()
    {
        
    }

    public function isEmpty()
    {
        return empty($this->config);
    }
}