<?php
/**
 * PHP version 7
 * File ObjectManagerInterface.php
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
 * Interface ObjectManagerInterface
 * 
 * @category Interface
 * @package  Framework\ObjectManager
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
interface ObjectManagerInterface
{
    /**
     * Method get
     *
     * @param string $name    shareObjectName
     * @param class  $factory ObjectOrFactory
     * 
     * @return Object
     */
    public function get($name, $factory = null);
}
