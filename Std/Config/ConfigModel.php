<?php
/**
 * PHP version 7
 * File ConfigModel.php
 *
 * @category Config
 * @package  Std\Config
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
declare(strict_types=1);

namespace Std\Config;

use Std\Config\ConfigModel\ConfigModelInterface;
use Std\Config\ConfigModel\AbstractConfigModel;

/**
 * Class ConfigModel
 *
 * @category Class
 * @package  Std\Config
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
        return ROOT_DIR . "Std/Config/" . $namespace . "/" . $configName . ".php";
    }
}
