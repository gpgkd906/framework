<?php
/**
 * PHP version 7
 * File RouterInterface.php
 *
 * @category Router
 * @package  Std\Router
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
declare(strict_types=1);

namespace Std\Router;

/**
 * Interface RouterInterface
 *
 * @category Interface
 * @package  Std\Router
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
interface RouterInterface
{
    const TRIGGER_ROUTERLIST_LOADED = 'router list loaded';
    const TRIGGER_ROUTEMISS = 'route miss';

    /**
     * Method isMatched
     *
     * @return mixed
     */
    public function isMatched();

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
