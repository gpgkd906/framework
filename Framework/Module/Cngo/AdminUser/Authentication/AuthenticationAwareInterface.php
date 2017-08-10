<?php
declare(strict_types=1);

namespace Framework\Module\Cngo\AdminUser\Authentication;

interface AuthenticationAwareInterface
{
    public function getAuthentication();

    public function setAuthentication(Authentication $Authentication);
}
