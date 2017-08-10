<?php
declare(strict_types=1);

namespace Framework\Service\CacheService;

use Framework\ObjectManager\SingletonInterface;
use Framework\Config\ConfigModel;
use Zend\Cache\StorageFactory;

class CacheService implements SingletonInterface
{
    use \Framework\ObjectManager\SingletonTrait;

    private $cachePool = [];
    private $default = null;

    private function __construct()
    {
        $config = ConfigModel::getConfigModel([
            "scope" => 'cache',
            "property" => ConfigModel::READONLY,
        ]);
        $this->default = $config->get('default');
        foreach ($config->get('storage') as $section => $options) {
            $this->setCache($section, StorageFactory::factory($options));
        }
    }

    public function getCache($section = null)
    {
        if ($section === null) {
            $section = $this->default;
        }
        if (isset($this->cachePool[$section])) {
            return $this->cachePool[$section];
        }
    }

    public function setCache($section, $cache)
    {
        $this->cachePool[$section] = $cache;
    }
}
