<?php
/**
 * PHP version 7
 * File CacheManager.php
 *
 * @category Service
 * @package  Std
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
declare(strict_types=1);

namespace Std\CacheManager;

use Framework\ObjectManager\SingletonInterface;
use Std\Config\ConfigModel;
use Zend\Cache\StorageFactory;
use Zend\Cache\Storage\FlushableInterface;

/**
 * Interface CacheManager
 *
 * @category Interface
 * @package  Std
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
class CacheManager implements SingletonInterface
{
    use \Framework\ObjectManager\SingletonTrait;

    private $_config = null;
    private $_cachePool = [];

    private function getConfig()
    {
        if ($this->_config === null) {
            $this->_config = ConfigModel::getConfigModel(
                [
                    "scope" => 'cache',
                    "property" => ConfigModel::READONLY,
                ]
            );
        }
        return $this->_config;
    }

    /**
     * Method getCache
     *
     * @param string $section cacheSection
     *
     * @return array|null
     */
    public function getCache(string $section)
    {
        if (isset($this->_cachePool[$section])) {
            return $this->_cachePool[$section];
        }
    }

    /**
     * Method setCache
     *
     * @param string     $section cacheSection
     * @param Zend\Cache $cache   cacheStorage
     *
     * @return this
     */
    public function setCache($section, $cache)
    {
        $this->_cachePool[$section] = $cache;
        return $this;
    }

    /**
     * 注册cache，对于不需要每次都初始化的cache，以及开发中随时追加的cache，进行注册
     * 未注册的cache无法通过CacheManager进行管理
     *
     * @param string     $section cacheSection
     * @param Zend\Cache $options cacheOptions
     *
     * @return this
     */
    public function registerCache($section, $options)
    {
        $this->setCache($section, StorageFactory::factory($options));
        return $this;
    }

    /**
     * 委托CacheManager自动分配一个缓存器。
     * 逻辑侧可以指定缓存类型，但不制定具体的缓存设定。
     * 逻辑侧也可以省略缓存类型，这种情况下，由CacheManager自动选择默认缓存类型
     * 逻辑侧委托已经存在的缓存器会产生错误。
     *
     * @param [type] $section
     * @param [type] $options
     * @return void
     */
    public function delegate($section, $options = null)
    {
        if ($cache = $this->getCache($section)) {
            return $cache;
        }
        $config = $this->getConfig();
        $delegate = $config->get('delegate');
        if (!is_array($options)) {
            if ($options === null || !isset($delegate[$options])) {
                $options = $delegate['default'];
            }
            $delegateOptions = $delegate['adapter'][$options];
            $delegateOptions['adapter']['options']['namespace'] .= $section;
            $options = $delegateOptions;
        }
        $this->registerCache($section, $options);
        return $this->getCache($section);
    }

    /**
     * 清除所有的缓存
     * ※Memcached是使所有缓存过期
     *
     * @return this
     */
    public function flushAll()
    {
        foreach ($this->_cachePool as $Cache) {
            $Cache->flush();
        }
        return $this;
    }
}
