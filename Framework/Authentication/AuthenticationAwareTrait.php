<?php

namespace Framework\Authentication;

trait AuthenticationAwareTrait
{
    private static $Authentication;

    public function getAuthentication()
    {
        return self::$Authentication;
    }

    public function setAuthentication(AuthenticationInterface $Authentication)
    {
        self::$Authentication = $Authentication;
    }
}
