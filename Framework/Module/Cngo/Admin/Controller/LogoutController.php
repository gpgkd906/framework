<?php

namespace Framework\Module\Cngo\Admin\Controller;

use Framework\Controller\AbstractController;
use Framework\ViewModel\ViewModel\ViewModelManager;
use Framework\Module\Cngo\Admin\View\ViewModel\LoginViewModel;
use Framework\Authentication\AuthenticationAwareInterface;
use Framework\Module\Cngo\Admin\Controller\AbstractAdminController;
use Framework\Module\Cngo\Admin\Controller\LoginController;

class LogoutController extends AbstractAdminController
{
    public function index()
    {
        $this->getAuthentication()->clearIdentity();
        $this->getRouter()->redirect(LoginController::class);
    }

    public static function getDescription()
    {
        return "ログアウト";
    }

    public static function getPriority()
    {
        return 99;
    }
}
