<?php

namespace Framework\Authentication;

use Framework\ObjectManager\FactoryInterface;
use Framework\Config\ConfigModel;
use Zend\Cache\StorageFactory;
use Zend\Session\SaveHandler\Cache;
use Zend\Session\Config\SessionConfig;
use Zend\Session\Storage\SessionArrayStorage;
use Zend\Session\SessionManager;

class SessionManagerFactory implements FactoryInterface
{
    public function create()
    {
        $config = ConfigModel::getConfigModel([
            "scope" => 'authentication',
            "property" => ConfigModel::READONLY,
        ]);
        $storage = StorageFactory::factory($config->get('storage'));
        $saveHandler = new Cache($storage);
        $SessionConfig = new SessionConfig();
        $SessionConfig->setOptions($config->get('session'));
        $Storage = new SessionArrayStorage();
        $SessionManager = new SessionManager($SessionConfig, $Storage, $saveHandler);
        return $SessionManager;
    }
}
