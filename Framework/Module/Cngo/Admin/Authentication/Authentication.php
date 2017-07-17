<?php

namespace Framework\Module\Cngo\Admin\Authentication;

use Framework\Authentication\AbstractAuthentication;
use Zend\Authentication\Result;

class Authentication extends AbstractAuthentication
{
    public function login($username, $password)
    {
        $adapter = new Adapter\Admin($username, $password);
        $result = $this->authenticate($adapter);
        return $result;
    }
}
