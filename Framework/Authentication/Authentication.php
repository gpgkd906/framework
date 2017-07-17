<?php

namespace Framework\Authentication;

use Framework\ObjectManager\ObjectManager;
use Framework\ObjectManager\ObjectManagerAwareInterface;
use Framework\ObjectManager\SingletonInterface;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Storage\Session;
use Framework\Authentication\Adapter\Common;
use Zend\Authentication\Result;

class Authentication extends AuthenticationService implements
    AuthenticationInterface,
    ObjectManagerAwareInterface,
    SingletonInterface
{
    use \Framework\ObjectManager\ObjectManagerAwareTrait;
    use \Framework\ObjectManager\SingletonTrait;

    public function __construct()
    {
        $ObjectManager = ObjectManager::getSingleton();
        $SessionManager = $ObjectManager->get(SessionManager::class);
        $Storage = new Session('Auth', Authentication::class, $SessionManager);
        $SessionManager->start();
        parent::__construct($Storage);
    }

    public function login($username, $password)
    {
        $adapter = new Common($username, $password);
        $result = $this->authenticate($adapter);
        if ($result->getCode() === Result::SUCCESS) {
            $this->getStorage()->write($this->getIdentity());
        }
        return $result;
    }

    public function updateIdentity($exIdentity)
    {
        $identity = (array) $this->getIdentity();
        $identity = array_merge($identity, (array) $exIdentity);
        $this->getStorage()->write($identity);
    }
}
