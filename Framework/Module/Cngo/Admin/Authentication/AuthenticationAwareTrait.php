<?php

namespace Framework\Module\Cngo\Admin\Authentication;

trait AuthenticationAwareTrait
{
    private static $Authentication;

    public function getAuthentication()
    {
        return self::$Authentication;
    }

    public function setAuthentication(Authentication $Authentication)
    {
        self::$Authentication = $Authentication;
    }
}
