<?php
/**
 * PHP version 7
 * File CacheService.php
 *
 * @category Service
 * @package  Framework\Service
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
declare(strict_types=1);

namespace Framework\Service\CacheService;

use Framework\ObjectManager\SingletonInterface;
use Framework\Config\ConfigModel;
use Zend\Cache\StorageFactory;
use Zend\Cache\Storage\FlushableInterface;

/**
 * Interface CacheService
 *
 * @category Interface
 * @package  Framework\Service
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
class CacheService implements SingletonInterface
{
    use \Framework\ObjectManager\SingletonTrait;

    private $_cachePool = [];
    private $_default = null;

    /**
     * Constructor
     * 对已经设定的cache进行初始化处理
     */
    private function __construct()
    {
        $config = ConfigModel::getConfigModel(
            [
                "scope" => 'cache',
                "property" => ConfigModel::READONLY,
            ]
        );
        $this->_default = $config->get('default');
        foreach ($config->get('storage') as $section => $options) {
            $this->setCache($section, StorageFactory::factory($options));
        }
    }

    /**
     * Method getCache
     *
     * @param string $section cacheSection
     *
     * @return array|null
     */
    public function getCache($section = null)
    {
        if ($section === null) {
            $section = $this->_default;
        }
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
     * 未注册的cache无法通过cacheService进行管理
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
