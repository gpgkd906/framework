<?php

namespace Framework\Module\Cngo\Admin\Controller;

use Framework\Controller\Controller\AbstractController;
use Framework\ViewModel\ViewModel\ViewModelManager;

class LoginController extends AbstractController
{

    public function index()
    {
        // $Session = $this->getObjectManager()->getSessionService();
        return ViewModelManager::getViewModel([
            'viewModel' => 'Admin\LoginViewModel',
            'listeners' => [
                'Complete' => [$this, 'onLoginComplete']
            ]
        ]);
    }

    public function onLoginComplete($LoginViewModel, $data)
    {
        $Session = $this->getObjectManager()->getSessionService();
        $Session->setSection('LoginView', $data);
        $this->addEventListener(AbstractController::TRIGGER_AFTER_ACTION, function() {
            $this->exChange(DashboardController::class);
        });
    }
}
