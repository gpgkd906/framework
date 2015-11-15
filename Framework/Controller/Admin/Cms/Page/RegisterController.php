<?php

namespace Framework\Controller\Admin\Cms\Page;

use Framework\Controller\Controller\AbstractController;
use Framework\ViewModel\ViewModel\ViewModelManager;

class RegisterController extends AbstractController
{    
    public function index()
    {
        return ViewModelManager::getViewModel([
            'viewModel' => 'Admin\Cms\PageRegisterViewModel',
            'listeners' => [
                'Complete' => [$this, 'onRegisterComplete']
            ]
        ]);
    }

    public function onRegisterComplete($formViewModel, $data)
    {
        /* $Session = $this->getServiceManager()->getSessionService(); */
        /* $Session->setSection('LoginView', $data); */
        /* $this->addEventListener(AbstractController::TRIGGER_AFTER_ACTION, function() { */
        /*     $this->exChange(DashboardController::class); */
        /* }); */
    }   
}
