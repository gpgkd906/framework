<?php
/**
 * PHP version 7
 * File RouterManagerAwareTrait.php
 *
 * @category Router
 * @package  Std\Router
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
declare(strict_types=1);

namespace Std\Router;

use Framework\ObjectManager\ObjectManager;

/**
 * Trait RouterManagerAwareTrait
 *
 * @category Trait
 * @package  Std\Router
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
trait RouterManagerAwareTrait
{
    private static $_RouterManager;

    /**
     * Method setRouterManager
     *
     * @param RouterManagerInterface $RouterManager RouterManager
     * @return this
     */
    public function setRouterManager(RouterManagerInterface $RouterManager)
    {
        self::$_RouterManager = $RouterManager;
        return $this;
    }

    /**
     * Method getRouterManager
     *
     * @return RouterManagerInterface $RouterManager
     */
    public function getRouterManager()
    {
        if (self::$_RouterManager === null) {
            self::$_RouterManager = ObjectManager::getSingleton()->get(RouterManagerInterface::class);
        }
        return self::$_RouterManager;
    }

    public function getRouter()
    {
        if (self::$_RouterManager) {
            // マッチしたルータがあれば、そのルータを返す。
            if(self::$_RouterManager->getMatched()) {
                return self::$_RouterManager->getMatched();
            } else {
                // マッチしたルータがなければ、デフォルトのルータを返す
                self::$_RouterManager->get(Router::class);
            }
        }
    }
}
