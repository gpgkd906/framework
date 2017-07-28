<?php

namespace Framework\Module\Cngo\AdminUser\Controller;

use Framework\Controller\AbstractController;
use Framework\ViewModel\ViewModelManager;
use Framework\Module\Cngo\AdminUser\View\ViewModel\LoginViewModel;
use Framework\Module\Cngo\Admin\Controller\AbstractAdminController;
use Framework\Module\Cngo\AdminUser\Controller\LoginController;
use Framework\Module\Cngo\AdminUser\Authentication\AuthenticationAwareInterface;

class LogoutController extends AbstractAdminController implements AuthenticationAwareInterface
{
    use \Framework\Module\Cngo\AdminUser\Authentication\AuthenticationAwareTrait;

    public function index()
    {
        $this->getAuthentication()->clearIdentity();
        $this->getRouter()->redirect(LoginController::class);
    }

    public static function getPageInfo()
    {
        return [
            "description" => "ログアウト",
            "priority" => 99,
            "menu" => true
        ];
    }
}