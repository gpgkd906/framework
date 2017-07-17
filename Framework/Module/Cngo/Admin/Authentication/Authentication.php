<?php

namespace Framework\Module\Cngo\Admin\Authentication;

use Framework\Authentication\Authentication as FrameworkAuthentication;
use Zend\Authentication\Result;

class Authentication extends FrameworkAuthentication
{
    public function login($username, $password)
    {
        $adapter = new Adapter\Admin($username, $password);
        $result = $this->authenticate($adapter);
        return $result;
    }
}
