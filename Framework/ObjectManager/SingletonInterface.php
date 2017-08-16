<?php
/**
 * PHP version 7
 * File SingletonInterface.php
 * 
 * @category Interface
 * @package  Framework\ObjectManager
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
declare(strict_types=1);

namespace Framework\ObjectManager;

/**
 * Interface SingletonInterface
 * 
 * @category Interface
 * @package  Framework\ObjectManager
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
interface SingletonInterface
{
    /**
     * Method getSingleton
     *
     * @return SingletonInterface this
     */
    public static function getSingleton();
}
