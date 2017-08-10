<?php
declare(strict_types=1);

namespace Framework\Authentication;

interface AuthenticationInterface
{
    public function login($username, $password);
    public function updateIdentity($Identity);
}
