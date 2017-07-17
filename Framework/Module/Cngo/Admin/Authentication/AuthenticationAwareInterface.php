<?php

namespace Framework\Module\Cngo\Admin\Authentication;

interface AuthenticationAwareInterface
{
    public function getAuthentication();

    public function setAuthentication(Authentication $Authentication);
}
