<?php

namespace Framework\Authentication\Adapter;

use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Result;

class Common implements AdapterInterface
{
    protected $username;
    protected $password;

    public function __construct($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    public function authenticate()
    {
        $Result = $this->doAuthenticate();
        if ($Result instanceof Result) {
            return $Result;
        }
        if ($Result) {
            return new Result(Result::SUCCESS, $this->username, ['Authenticated successfully.']);
        } else {
            return new Result(Result::FAILURE_CREDENTIAL_INVALID, null, ['Invalid credentials.']);
        }
    }

    public function doAuthenticate()
    {
        return true;
    }
}
