<?php
namespace Framework\Repository;

use Framework\ObjectManager\FactoryInterface;
use Framework\Config\ConfigModel;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\Cache\RedisCache;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\MemcacheCache;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;

class DoctrineFactory implements FactoryInterface
{
    public function create()
    {
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
        $dbParams = array(
            'driver'   => $connection['driver'],
            'user'     => $connection['user'],
            'password' => $connection['password'],
            'dbname'   => $connection['dsn']['dbname'],
            'host'   => $connection['dsn']['host'],
            'charset'   => $connection['dsn']['charset'],
        );
        $cache = $this->getCache($cache);
        $driver = new AnnotationDriver(new AnnotationReader(), $paths);
        AnnotationRegistry::registerLoader('class_exists');
        $config = Setup::createConfiguration($isDevMode, $proxyDir, $cache);
        $config->setMetadataDriverImpl($driver);
        return EntityManager::create($dbParams, $config);
    }

    private function getCache($config)
    {
        $Cache = null;
        switch ($config['type']) {
            case 'redis':
                $redis = new \Redis();
                $redis->connect($config['connection']['host'], $config['connection']['port']);
                $Cache = new RedisCache();
                $Cache->setRedis($redis);
                break;
            case 'memcache':
                break;
            case 'array':
                $Cache = new ArrayCache();
                break;
        }
        return $Cache;
    }
}