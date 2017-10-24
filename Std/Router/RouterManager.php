<?php
/**
 * PHP version 7
 * File RouterManager.php
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
 * Interface RouterManager
 *
 * @category Interface
 * @package  Std\Router
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
class RouterManager implements RouterManagerInterface
{
    private $_routerPool = [];
    private $_matched = null;
    /**
     * register ルータを登録する
     *
     * @param string $namespace
     * @param RouterInterface $router
     * @return void
     */
    public function register(string $namespace, RouterInterface $router)
    {
        if (isset($this->_routerPool[$namespace])) {
            throw new Exception(sprintf('すでに該当する名前空間「%s」のルータが登録されている。', $namespace));
        }
        $this->_routerPool[$namespace] = $router;
    }

    /**
     * get 指定する名前空間のルータを取得する
     *
     * @param string $namespace
     * @return void
     */
    public function get($namespace = null)
    {
        if ($namespace === null) {
            $namespace = __NAMESPACE__;
        }
        if (isset($this->_routerPool[$namespace])) {
            return $this->_routerPool[$namespace];
        }
    }

    /**
     * getMatched isMatchedのルータを取得する
     *
     * @return void
     */
    public function getMatched()
    {
        if ($this->_matched === null) {
            foreach ($this->getRouters() as $Router) {
                if ($Router->isMatched()) {
                    $this->_matched = $Router;
                    break;
                }
            }
        }
        return $this->_matched;
    }

    public function getMatchedOrDefault()
    {
        if ($this->getMatched()) {
            return $this->getMatched();
        }
        return $this->get(__NAMESPACE__);
    }

    public function getRouters()
    {
        return $this->_routerPool;
    }
}
