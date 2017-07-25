<?php

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

abstract class AbstractAuthentication extends AuthenticationService implements
    AuthenticationInterface,
    ObjectManagerAwareInterface,
    SingletonInterface
{
    use \Framework\ObjectManager\ObjectManagerAwareTrait;
    use \Framework\ObjectManager\SingletonTrait;
    private static $crypt = null;

    public function __construct(Storage\StorageInterface $Storage = null, Adapter\AdapterInterface $Adapter = null)
    {
        $ObjectManager = ObjectManager::getSingleton();
        $SessionService = $ObjectManager->get(SessionService::class);
        if ($Storage === null) {
            $Storage = new Storage\Session('Auth', Authentication::class, $SessionService->getSessionManager());
        }
        parent::__construct($Storage, $Adapter);
    }

    abstract public function login($username, $password);

    public function updateIdentity($exIdentity)
    {
        $identity = (array) $this->getIdentity();
        $identity = array_merge($identity, (array) $exIdentity);
        $this->getStorage()->write($identity);
    }

    public function passwordHash($password)
    {
        $crypt = self::getCrypt();
        return $crypt->create($password);
    }

    public static function getCrypt()
    {
        if (self::$crypt === null) {
            self::$crypt = new Bcrypt();
        }
        return self::$crypt;
    }
}
