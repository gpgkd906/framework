<?php
/**
 * PHP version 7
 * File RouterAwareTrait.php
 * 
 * @category Router
 * @package  Framework\Router
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
declare(strict_types=1);

namespace Framework\Router;

use Framework\ObjectManager\ObjectManager;

/**
 * Trait RouterAwareTrait
 * 
 * @category Trait
 * @package  Framework\Router
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
trait RouterAwareTrait
{
    private static $_Router;

    /**
     * Method setRouter
     *
     * @param RouterInterface $Router Router
     * @return this
     */
    public function setRouter(RouterInterface $Router)
    {
        self::$_Router = $Router;
        return $this;
    }

    /**
     * Method getRouter
     *
     * @return RouterInterface $Router
     */
    public function getRouter()
    {
        if (self::$_Router === null) {
            self::$_Router = ObjectManager::getSingleton()->get(RouterInterface::class);
        }
        return self::$_Router;
    }
}
