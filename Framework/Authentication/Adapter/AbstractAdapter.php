<?php

namespace Framework\Authentication\Adapter;

use Zend\Authentication\Adapter\AdapterInterface;
use Framework\Authentication\AbstractAuthentication;

abstract class AbstractAdapter implements AdapterInterface
{
    protected $username;
    protected $password;

    public function __construct($username = null, $password = null)
    {
        $this->username = $username;
        $this->password = $password;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function getPassword()
    {
        return $this->password;
    }

    abstract public function authenticate();

    public function getCrypt()
    {
        return AbstractAuthentication::getCrypt();
    }
}
