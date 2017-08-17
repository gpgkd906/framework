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
 * Interface RouterAwareInterface
 * 
 * @category Interface
 * @package  Framework\Router
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
interface RouterAwareInterface
{
    /**
     * Method setRouter
     *
     * @param RouterInterface $Router Router
     * @return mixed
     */
    public function setRouter(RouterInterface $Router);

    /**
     * Method getRouter
     *
     * @return RouterInterface $Router
     */
    public function getRouter();
}
