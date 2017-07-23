<?php

namespace Framework\Authentication;

use Framework\ObjectManager\ObjectManager;
use Framework\ObjectManager\ObjectManagerAwareInterface;
use Framework\ObjectManager\SingletonInterface;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Storage\Session;
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

    public function __construct()
    {
        $ObjectManager = ObjectManager::getSingleton();
        $SessionService = $ObjectManager->get(SessionService::class);
        $Storage = new Session('Auth', Authentication::class, $SessionService->getSessionManager());
        parent::__construct($Storage);
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
