<?php

namespace Framework\Authentication;

trait AuthenticationAwareTrait
{
    private $Authentication;

    public function getAuthentication()
    {
        return $this->Authentication;
    }

    public function setAuthentication(AuthenticationInterface $Authentication)
    {
        $this->Authentication = $Authentication;
    }
}
