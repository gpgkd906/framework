<?php

namespace Framework\Authentication\Adapter;

use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Crypt\Password\Bcrypt;

abstract class AbstractAdapter implements AdapterInterface
{
    protected $username;
    protected $password;
    private $ctypt = null;

    public function __construct($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    abstract public function authenticate();

    public function getCrypt()
    {
        if ($this->ctypt === null) {
            $this->ctypt = new Bcrypt();
        }
        return $this->ctypt;
    }
}
