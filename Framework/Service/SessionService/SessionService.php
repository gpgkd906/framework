<?php
declare(strict_types=1);

namespace Framework\Service\SessionService;

use Framework\ObjectManager\SingletonInterface;
use Framework\Config\ConfigModel;
use Zend\Cache\StorageFactory;
use Zend\Session\SaveHandler\Cache;
use Zend\Session\Config\SessionConfig;
use Zend\Session\Storage\SessionArrayStorage;
use Zend\Session\SessionManager;
use Zend\Session\Container;

class SessionService implements SingletonInterface
{
    use \Framework\ObjectManager\SingletonTrait;

    private $sessionStorage = [];
    private $SessionManager = null;

    private function __construct()
    {
    }

    public function getSessionManager()
    {
        if ($this->SessionManager === null) {
            $config = ConfigModel::getConfigModel([
                "scope" => 'session',
                "property" => ConfigModel::READONLY,
            ]);
            $storage = StorageFactory::factory($config->get('storage'));
            $saveHandler = new Cache($storage);
            $SessionConfig = new SessionConfig();
            $SessionConfig->setOptions($config->get('options'));
            $Storage = new SessionArrayStorage();
            $this->SessionManager = new SessionManager($SessionConfig, $Storage, $saveHandler);
            Container::setDefaultManager($this->SessionManager);
        }
        return $this->SessionManager;
    }

    public function getSession($namespace)
    {
        if (!isset($this->sessionStorage[$namespace])) {
            $this->setSession($namespace, $this->createContainer($namespace));
        }
        return $this->sessionStorage[$namespace];
    }

    private function setSession($namespace, $storage)
    {
        $this->sessionStorage[$namespace] = $storage;
    }

    private function createContainer($namespace)
    {
        return new Container($namespace);
    }
}
