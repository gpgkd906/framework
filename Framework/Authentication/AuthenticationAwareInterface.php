<?php

namespace Framework\Authentication;

interface AuthenticationAwareInterface
{
    public function getAuthentication();

    public function setAuthentication(AuthenticationInterface $Authentication);
}
