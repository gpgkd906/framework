<?php

namespace Framework\Controller\Admin;

use Framework\Controller\Controller\AbstractController;
use Framework\ViewModel\ViewModel\ViewModelManager;

class LoginController extends AbstractController
{
    
    public function index()
    {
        $Session = $this->getServiceManager()->getSessionService();
        return ViewModelManager::getViewModel([
            'viewModel' => 'Admin\LoginViewModel',
            'listeners' => [
                'Complete' => [$this, 'onLoginComplete']
            ]
        ]);
    }

    public function onLoginComplete($LoginViewModel, $data)
    {
        $Session = $this->getServiceManager()->getSessionService();
        $Session->setSection('LoginView', $data);
        $this->addEventListener(AbstractController::TRIGGER_AFTER_ACTION, function() {
            $this->exChange(DashboardController::class);
        });
    }
}
