<?php

namespace Framework\Module\Cngo\Admin\Controller;

use Framework\Controller\AbstractController;
use Framework\ViewModel\ViewModelManager;
use Framework\Module\Cngo\Admin\View\ViewModel\LoginViewModel;
use Framework\Module\Cngo\Admin\Authentication\AuthenticationAwareInterface;

class LoginController extends AbstractController implements AuthenticationAwareInterface
{
    use \Framework\Module\Cngo\Admin\Authentication\AuthenticationAwareTrait;

    public function index()
    {
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
        if ($ViewModel->getForm()->validate()) {
            $loginInfo = $ViewModel->getForm()->getData()['default'];
            $this->getAuthentication()->login($loginInfo['login'], $loginInfo['password']);
            if ($this->getAuthentication()->hasIdentity()) {
                $this->addEventListener(AbstractController::TRIGGER_AFTER_ACTION, function () {
                    $this->getRouter()->redirect(DashboardController::class);
                });
            }
        } else {
            var_Dump($ViewModel->getForm()->getMessage());
        }
    }

    public static function getDescription()
    {
        return "ログイン";
    }

    public static function getPriority()
    {
        return 0;
    }
}
