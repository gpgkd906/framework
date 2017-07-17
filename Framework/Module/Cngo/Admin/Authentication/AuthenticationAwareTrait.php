<?php

namespace Framework\Module\Cngo\Admin\Authentication;

trait AuthenticationAwareTrait
{
    private $Authentication;

    public function getAuthentication()
    {
        return $this->Authentication;
    }

    public function setAuthentication(Authentication $Authentication)
    {
        $this->Authentication = $Authentication;
    }
}
