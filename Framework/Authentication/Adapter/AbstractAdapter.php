<?php

namespace Framework\Authentication\Adapter;

use Zend\Authentication\Adapter\AdapterInterface;
use Framework\Authentication\AbstractAuthentication;

abstract class AbstractAdapter implements AdapterInterface
{
    protected $username;
    protected $password;

    public function __construct($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    abstract public function authenticate();

    public function getCrypt()
    {
        return AbstractAuthentication::getCrypt();
    }
}
