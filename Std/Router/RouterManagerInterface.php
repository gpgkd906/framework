<?php
/**
 * PHP version 7
 * File RouterManagerInterface.php
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
 * Interface RouterManagerInterface
 *
 * @category Interface
 * @package  Std\Router
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
interface RouterManagerInterface
{
    /**
     * register ルータを登録する
     *
     * @param string $namespace
     * @param RouterInterface $router
     * @return void
     */
    public function register(string $namespace, RouterInterface $router);

    /**
     * get 指定名前空間のルータを取得する
     *
     * @param string $namespace
     * @return void
     */
    public function get($namespace);

    /**
     * getMatched isMatchedのルータを取得する
     *
     * @return void
     */
    public function getMatched();
}
