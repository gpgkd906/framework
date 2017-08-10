<?php
declare(strict_types=1);

namespace Framework\Config\ConfigModel;

use Exception;

abstract class AbstractConfigModel implements ConfigModelInterface
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
    //
    const INVALID_SCOPE_OR_FILEPATH = "error: invalid scope or config-filepath";

    static private $namespace = null;
    static private $instances = [];

    private $scope = null;
    private $config = [];
    private $property;
    private $type = null;

    public static function registerNamespace($namespace)
    {
        return self::$namespace = $namespace;
    }

    public static function getConfigModel($metaConfig)
    {
        $namespace = self::$namespace;
        $scope = $metaConfig["scope"];
        if (isset($metaConfig["property"])) {
            $property = $metaConfig["property"];
        } else {
            $property = self::READONLY;
        }
        if (isset($metaConfig['type'])) {
            $type = $metaConfig['type'];
        } else {
            $type = self::TYPE_PHP_ARRAY;
        }
        $configName = join(".", [$namespace, $scope, $property]);
        if (!isset(self::$instances[$configName])) {
            $config = self::loadConfig($metaConfig, $type);
            self::$instances[$configName] = new static($scope, $config, $property, $type);
        }
        return self::$instances[$configName];
    }

    static public function loadConfig($metaConfig, $type)
    {
        if (!isset($metaConfig['scope']) && !isset($metaConfig['file'])) {
            throw new Exception(self::INVALID_SCOPE_OR_FILEPATH);
        }
        if (isset($metaConfig['file'])) {
            $configFile = $metaConfig['file'];
        } else {
            if (isset($metaConfig['namespace'])) {
                $namespace = $metaConfig['namespace'];
            } else {
                $namespace = self::$namespace;
            }
            $configFile = static::getFile($namespace, $metaConfig['scope']);
        }
        if (is_file($configFile)) {
            switch ($type) {
                case self::TYPE_PHP_ARRAY:
                    $config = require($configFile);
                    break;
                case self::TYPE_INI_FILE:
                    $config = parse_ini_file($configFile, true);
                    break;
                case self::TYPE_JSON_FILE:
                    $config = json_decode(file_get_contents($configFile), true);
                    break;
            }
        } else {
            $config = [];
        }
        return $config;
    }

    protected function __construct($scope, $config, $property, $type)
    {
        $this->scope = $scope;
        $this->config = $config;
        $this->property = $property;
        $this->type = $type;
    }

    abstract protected static function getFile($namespace, $configName);

    public function getScope()
    {
        return $this->scope;
    }

    public function get($key = null, $default = null)
    {
        if ($key === null) {
            return $this->config;
        }
        if (isset($this->config[$key])) {
            return $this->config[$key];
        }
        return $default;
    }

    public function set($key, $value)
    {
        if (isset($this->config[$key])) {
            $this->config[$key] = $value;
        }
        return $value;
    }
}
