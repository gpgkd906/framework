<?php
/**
 * PHP version 7
 * File RouterInterface.php
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
 * Interface RouterInterface
 * 
 * @category Interface
 * @package  Framework\Router
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
interface RouterInterface
{
    /**
     * Method dispatch
     *
     * @return mixed
     */
    public function dispatch();

    /**
     * Method getRouterList
     *
     * @return array RouterList
     */
    public function getRouterList();
}
