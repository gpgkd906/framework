<?php
/**
 * PHP version 7
 * File AuthenticationInterface.php
 * 
 * @category Authentication
 * @package  Framework\Application
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
declare(strict_types=1);

namespace Framework\Authentication;

use Framework\ObjectManager\ObjectManager;
use Framework\ObjectManager\ObjectManagerAwareInterface;
use Framework\ObjectManager\SingletonInterface;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Storage;
use Zend\Authentication\Adapter;
use Framework\Authentication\Adapter\Common;
use Zend\Authentication\Result;
use Framework\Service\SessionService\SessionService;
use Zend\Crypt\Password\Bcrypt;

/**
 * Interface AuthenticationInterface
 * 
 * @category Authentication
 * @package  Framework\Authentication
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
abstract class AbstractAuthentication extends AuthenticationService implements
    AuthenticationInterface,
    ObjectManagerAwareInterface,
    SingletonInterface
{
    use \Framework\ObjectManager\ObjectManagerAwareTrait;
    use \Framework\ObjectManager\SingletonTrait;
    private static $_crypt = null;

    /**
     * Method __construct
     *
     * @param Storage\StorageInterface $Storage Storage
     * @param Adapter\AdapterInterface $Adapter Adapter
     */
    public function __construct(Storage\StorageInterface $Storage = null, Adapter\AdapterInterface $Adapter = null)
    {
        $ObjectManager = ObjectManager::getSingleton();
        $SessionService = $ObjectManager->get(SessionService::class);
        if ($Storage === null) {
            $Storage = new Storage\Session('Auth', Authentication::class, $SessionService->getSessionManager());
        }
        parent::__construct($Storage, $Adapter);
    }

    /**
     * Abstract Method login
     *
     * @param string $username UserName
     * @param string $password Password
     * 
     * @return void
     */
    abstract public function login($username, $password);

    /**
     * Method updateIdentity
     *
     * @param array $exIdentity Identity
     * 
     * @return void
     */
    public function updateIdentity($exIdentity)
    {
        $identity = (array) $this->getIdentity();
        $identity = array_merge($identity, (array) $exIdentity);
        $this->getStorage()->write($identity);
    }

    /**
     * Method passwordHash
     *
     * @param string $password password
     * 
     * @return string hash
     */
    public function passwordHash($password)
    {
        $crypt = self::getCrypt();
        return $crypt->create($password);
    }

    /**
     * Method getCrypt
     *
     * @return Bcrypt self::$_crypt
     */
    public static function getCrypt()
    {
        if (self::$_crypt === null) {
            self::$_crypt = new Bcrypt();
        }
        return self::$_crypt;
    }
}
