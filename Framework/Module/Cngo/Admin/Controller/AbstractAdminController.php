<?php

namespace Framework\Module\Cngo\Admin\Controller;

use Framework\Controller\AbstractController;
use Framework\Module\Cngo\Admin\Controller\LoginController;
use Framework\Module\Cngo\Admin\Authentication\AuthenticationAwareInterface;

class AbstractAdminController extends AbstractController implements AuthenticationAwareInterface
{
    use \Framework\Module\Cngo\Admin\Authentication\AuthenticationAwareTrait;

    protected function __construct()
    {
        $this->addEventListener(self::TRIGGER_BEFORE_ACTION, [$this, 'adminAuthentication']);
        parent::__construct();
    }

    public function adminAuthentication($event)
    {
        if (!$this->getAuthentication()->hasIdentity()) {
            $this->getRouter()->redirect(LoginController::class);
        }
    }
}
