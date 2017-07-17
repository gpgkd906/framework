<?php

namespace Framework\Module\Cngo\Admin\Authentication\Adapter;

use Framework\Authentication\Adapter\Common;
use Zend\Authentication\Result;

class Admin extends Common
{
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
