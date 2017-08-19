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
     */
    private function __construct()
    {
        $config = ConfigModel::getConfigModel([
            "scope" => 'cache',
            "property" => ConfigModel::READONLY,
        ]);
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
}
