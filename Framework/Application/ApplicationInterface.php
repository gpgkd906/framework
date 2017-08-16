<?php
/**
 * PHP version 7
 * File ApplicationInterface.php
 * 
 * @category Module
 * @package  Framework\Application
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
declare(strict_types=1);
namespace Framework\Application;

/**
 * Interface ApplicationInterface
 * 
 * @category Application
 * @package  Framework\Application
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
interface ApplicationInterface
{
    /**
     * Method getConfig
     *
     * @return mixed config
     */
    public function getConfig();
    
    /**
     * Method setConfig
     *
     * @param array $config Config
     * 
     * @return this
     */
    public function setConfig($config);

    /**
     * Method run
     *
     * @return void
     */
    public function run();
}
