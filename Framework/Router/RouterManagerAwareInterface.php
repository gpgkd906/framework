<?php
/**
 * PHP version 7
 * File RouterAwareInterface.php
 *
 * @category Router
 * @package  Framework\Router
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
declare(strict_types=1);

namespace Framework\Router;

/**
 * Interface RouterManagerAwareInterface
 *
 * @category Interface
 * @package  Framework\Router
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
interface RouterManagerAwareInterface
{
    /**
     * Method setRouterManager
     *
     * @param RouterManagerInterface $RouterManager RouterManager
     * @return mixed
     */
    public function setRouterManager(RouterManagerInterface $RouterManager);

    /**
     * Method getRouterManager
     *
     * @return RouterManagerInterface $RouterManager
     */
    public function getRouterManager();
}
