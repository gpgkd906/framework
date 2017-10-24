<?php
/**
 * PHP version 7
 * File AbstractConfigModel.php
 *
 * @category Config
 * @package  Std\Config
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
declare(strict_types=1);

namespace Std\Config\ConfigModel;

use Exception;

/**
 * Abstract Class AbstractConfigModel
 *
 * @category Class
 * @package  Std\Config
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
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

    static private $_namespace = null;
    static private $_instances = [];

    private $_scope = null;
    private $_config = [];
    private $_property;
    private $_type = null;

    /**
     * Method registerNamespace
     *
     * @param string $namespace Namespace
     *
     * @return void
     */
    public static function registerNamespace($namespace)
    {
        return self::$_namespace = $namespace;
    }

    /**
     * Method getConfigModel
     *
     * @param array $metaConfig metaConfig
     *
     * @return ConfigModelInterface
     */
    public static function getConfigModel($metaConfig)
    {
        $namespace = self::$_namespace;
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
        if (!isset(self::$_instances[$configName])) {
            $config = self::loadConfig($metaConfig, $type);
            self::$_instances[$configName] = new static($scope, $config, $property, $type);
        }
        return self::$_instances[$configName];
    }

    /**
     * Method loadConfig
     *
     * @param array  $metaConfig metaConfig
     * @param string $type       type
     *
     * @return array $config
     */
    public static function loadConfig($metaConfig, $type)
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
                $namespace = self::$_namespace;
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

    /**
     * Method __construct
     *
     * @param string $scope    scope
     * @param array  $config   config
     * @param string $property property
     * @param string $type     type
     */
    protected function __construct($scope, $config, $property, $type)
    {
        $this->_scope = $scope;
        $this->_config = $config;
        $this->_property = $property;
        $this->_type = $type;
    }

    /**
     * Abstract Method getFile
     *
     * @param string $namespace  Namespace
     * @param string $configName ConfigName
     *
     * @return string $configFile
     */
    abstract protected static function getFile($namespace, $configName);

    /**
     * Method getScope
     *
     * @return string scope
     */
    public function getScope()
    {
        return $this->_scope;
    }

    /**
     * Method get
     *
     * @param string $key     configKey
     * @param mixed  $default defaultValue
     *
     * @return mixed config
     */
    public function get($key = null, $default = null)
    {
        if ($key === null) {
            return $this->_config;
        }
        if (isset($this->_config[$key])) {
            return $this->_config[$key];
        }
        return $default;
    }

    /**
     * Method set
     *
     * @param string $key   configKey
     * @param mixed  $value configValue
     *
     * @return mixed $value
     */
    public function set($key, $value)
    {
        if (isset($this->_config[$key])) {
            $this->_config[$key] = $value;
        }
        return $value;
    }
}
