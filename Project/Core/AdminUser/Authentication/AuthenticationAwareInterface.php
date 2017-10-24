<?php
declare(strict_types=1);

namespace Project\Core\AdminUser\Authentication;

interface AuthenticationAwareInterface
{
    public function getAuthentication();

    public function setAuthentication(Authentication $Authentication);
}
