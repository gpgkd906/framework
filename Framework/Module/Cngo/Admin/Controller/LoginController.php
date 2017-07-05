<?php

namespace Framework\Module\Cngo\Admin\Controller;

use Framework\Controller\AbstractController;
use Framework\Service\SessionService\SessionServiceAwareInterface;
use Framework\Service\AdminService\AdminServiceAwareInterface;
use Framework\ViewModel\ViewModel\ViewModelManager;
use Framework\Module\Cngo\Admin\View\ViewModel\LoginViewModel;

class LoginController extends AbstractController implements AdminServiceAwareInterface
{
    use \Framework\Service\SessionService\SessionServiceAwareTrait;
    use \Framework\Service\AdminService\AdminServiceAwareTrait;

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
            $SessionService = $this->getSessionService();
            $SessionService->setSection('LoginView', $event->getData());
            $this->addEventListener(AbstractController::TRIGGER_AFTER_ACTION, function () {
                $this->getRouter()->redirect(DashboardController::class);
            });
        } else {
            var_Dump($ViewModel->getForm()->getMessage());
        }
    }
}
