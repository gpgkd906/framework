<?php

namespace Framework\Module\Cngo\AdminUser\Controller;

use Framework\Controller\AbstractController;
use Framework\ViewModel\ViewModelManager;
use Framework\Module\Cngo\AdminUser\View\ViewModel\LoginViewModel;
use Framework\Module\Cngo\AdminUser\Authentication\AuthenticationAwareInterface;
use Framework\Module\Cngo\Admin\Controller\DashboardController;

class LoginController extends AbstractController implements AuthenticationAwareInterface
{
    use \Framework\Module\Cngo\AdminUser\Authentication\AuthenticationAwareTrait;

    public function index()
    {
        if ($this->getAuthentication()->hasIdentity()) {
            $this->getRouter()->redirect(DashboardController::class);
        }
        return ViewModelManager::getViewModel([
            'viewModel' => LoginViewModel::class,
            'listeners' => [
                LoginViewModel::TRIGGER_FORMCOMPLETE => [$this, 'onLoginComplete']
            ]
        ]);
    }

    public function onLoginComplete(\Framework\EventManager\Event $event)
    {
        $ViewModel = $event->getTarget();
        if ($ViewModel->getForm()->isValid()) {
            $adminLogin = $ViewModel->getForm()->getData()['adminLogin'];
            $this->getAuthentication()->login($adminLogin['login'], $adminLogin['password']);
            if ($this->getAuthentication()->hasIdentity()) {
                $this->getRouter()->redirect(DashboardController::class);
            } else {
                $ViewModel->getForm()->login->forceError('test');
            }
        }
    }

    public static function getPageInfo()
    {
        return [
            "description" => "ログイン",
            "priority" => 0,
            "menu" => false
        ];
    }
}