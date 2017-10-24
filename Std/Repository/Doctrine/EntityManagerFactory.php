<?php
/**
 * PHP version 7
 * File EntityManagerFactory.php
 *
 * @category Factory
 * @package  Framework\Repositry
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
declare(strict_types=1);
namespace Std\Repository\Doctrine;

use Framework\ObjectManager\FactoryInterface;
use Std\Config\ConfigModel;
use Std\Repository\RepositoryManager;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\Cache\RedisCache;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\MemcachedCache;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;

/**
 * Factory EntityManagerFactory
 *
 * @category Factory
 * @package  Framework\Repositry
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
class EntityManagerFactory implements FactoryInterface
{
    private $_EntityManager = null;

    /**
     * Method create
     *
     * @param ObjectManager $ObjectManager ObjectManager
     *
     * @return EntityManager $entityManager
     */
    public function create($ObjectManager)
    {
        if ($this->_EntityManager === null) {
            $config = ConfigModel::getConfigModel([
                "scope" => ConfigModel::MODEL,
                "property" => ConfigModel::READONLY,
            ]);
            $connection = $config->get('connection');
            $entityManagerConfig = $config->get('entityManager');
            $cache = $config->get('cache');
            $RepositoryManager = RepositoryManager::getSingleton();
            $paths = $RepositoryManager->getEntityPath();
            $isDevMode = $entityManagerConfig['devMode'];
            $proxyDir = $entityManagerConfig['proxyDir'] ? $entityManagerConfig['proxyDir'] : __DIR__ . '/Proxy';
            $dbParams = [
                'driver'   => $connection['driver'],
                'user'     => $connection['user'],
                'password' => $connection['password'],
                'dbname'   => $connection['dsn']['dbname'],
                'host'   => $connection['dsn']['host'],
                'charset'   => $connection['dsn']['charset'],
            ];
            $cache = $this->_getCache($cache);
            $driver = new AnnotationDriver(new AnnotationReader(), $paths);
            AnnotationRegistry::registerLoader('class_exists');
            $config = Setup::createConfiguration($isDevMode, $proxyDir, $cache);
            $config->setMetadataDriverImpl($driver);
            $this->_EntityManager = EntityManager::create($dbParams, $config);
        }
        return $this->_EntityManager;
    }

    /**
     * Method _getCache
     *
     * @param array $config EntityConfig
     *
     * @return Cache $cache DoctrineCache
     */
    private function _getCache($config)
    {
        $Cache = null;
        switch ($config['type']) {
            case 'redis':
                $redis = new \Redis();
                $redis->connect($config['connection']['host'], $config['connection']['port']);
                $Cache = new RedisCache();
                $Cache->setRedis($redis);
                break;
            case 'memcached':
                $memcache = new \Memcached();
                $memcache->addServer($config['connection']['host'], $config['connection']['port']);
                $Cache = new MemcachedCache();
                $Cache->setMemcached($memcache);
                break;
            case 'array':
                $Cache = new ArrayCache();
                break;
        }
        return $Cache;
    }
}
