<?php
/**
 * PHP version 7
 * File SessionService.php
 * 
 * @category Service
 * @package  Framework\Service
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
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

/**
 * Class SessionService
 * 
 * @category Class
 * @package  Framework\Service
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
class SessionService implements SingletonInterface
{
    use \Framework\ObjectManager\SingletonTrait;

    private $_sessionStorage = [];
    private $_SessionManager = null;

    /**
     * Constructor
     */
    private function __construct()
    {
    }

    /**
     * Method getSessionManager
     *
     * @return SessionManager
     */
    public function getSessionManager()
    {
        if ($this->_SessionManager === null) {
            $config = ConfigModel::getConfigModel([
                "scope" => 'session',
                "property" => ConfigModel::READONLY,
            ]);
            $storage = StorageFactory::factory($config->get('storage'));
            $saveHandler = new Cache($storage);
            $SessionConfig = new SessionConfig();
            $SessionConfig->setOptions($config->get('options'));
            $Storage = new SessionArrayStorage();
            $this->_SessionManager = new SessionManager($SessionConfig, $Storage, $saveHandler);
            Container::setDefaultManager($this->_SessionManager);
        }
        return $this->_SessionManager;
    }

    /**
     * Method getSession
     *
     * @param string $namespace Namespace
     * 
     * @return SessionStorage
     */
    public function getSession($namespace)
    {
        if (!isset($this->_sessionStorage[$namespace])) {
            $this->setSession($namespace, $this->createContainer($namespace));
        }
        return $this->_sessionStorage[$namespace];
    }

    /**
     * Method setSession
     *
     * @param string         $namespace Namespace
     * @param SessionStorage $storage   SessionStorage
     * 
     * @return this
     */
    private function setSession($namespace, $storage)
    {
        $this->_sessionStorage[$namespace] = $storage;
        return $this;
    }

    /**
     * Method createContainer
     *
     * @param string $namespace Namespace
     * 
     * @return Container $container
     */
    private function createContainer($namespace)
    {
        return new Container($namespace);
    }
}
