<?php

namespace Framework\Module\Cngo\Admin\Authentication;

use Framework\ObjectManager\ObjectManager;

trait AuthenticationAwareTrait
{
    private static $Authentication;

    public function getAuthentication()
    {
        if (self::$Authentication === null) {
            self::$Authentication = ObjectManager::getSingleton()->get(Authentication::class);
        }
        return self::$Authentication;
    }

    public function setAuthentication(Authentication $Authentication)
    {
        self::$Authentication = $Authentication;
    }
}
