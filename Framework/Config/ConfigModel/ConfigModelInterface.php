<?php
/**
 * PHP version 7
 * File ConfigModelInterface.php
 * 
 * @category Config
 * @package  Framework\Config
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
declare(strict_types=1);

namespace Framework\Config\ConfigModel;

/**
 * Interface ConfigModelInterface
 * 
 * @category Interface
 * @package  Framework\Config
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
interface ConfigModelInterface
{
    /**
     * Method registerNamespace
     *
     * @param string $namespace Namespace
     * 
     * @return void
     */
    public static function registerNamespace($namespace);

    /**
     * Method getConfigModel
     *
     * @param array $config configArray
     * 
     * @return ConfigModelInterface
     */
    public static function getConfigModel($config);

    /**
     * Method get
     *
     * @param string $key configKey
     * 
     * @return config
     */
    public function get($key);

    /**
     * Method set
     *
     * @param string $key  configKey
     * @param mixed $value config
     * @return this
     */
    public function set($key, $value);
}
