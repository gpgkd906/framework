<?php
/**
 * PHP version 7
 * File ConfigModel.php
 * 
 * @category Config
 * @package  Framework\Config
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
declare(strict_types=1);

namespace Framework\Config;

use Framework\Config\ConfigModel\ConfigModelInterface;
use Framework\Config\ConfigModel\AbstractConfigModel;

/**
 * Class ConfigModel
 * 
 * @category Class
 * @package  Framework\Config
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
class ConfigModel extends AbstractConfigModel implements ConfigModelInterface
{
    /**
     * Method getFile
     *
     * @param string $namespace  Namespace
     * @param string $configName configName
     * 
     * @return string $configFile
     */
    protected static function getFile($namespace, $configName)
    {
        return ROOT_DIR . "Framework/Config/" . $namespace . "/" . $configName . ".php";
    }
}
