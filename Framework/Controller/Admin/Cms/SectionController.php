<?php

namespace Framework\Controller\Admin\Cms;

use Framework\Controller\Controller\AbstractController;
use Framework\ViewModel\ViewModel\ViewModelManager;

class SectionController extends AbstractController
{
    public function register()
    {
        return ViewModelManager::getViewModel([
            'viewModel' => 'Admin\Cms\Section\RegisterViewModel',
            'listeners' => [
                'Complete' => [$this, 'onLoginComplete']
            ]
        ]);
    }

    public function onLoginComplete($formViewModel, $data)
    {
        /* $Session = $this->getServiceManager()->getSessionService(); */
        /* $Session->setSection('LoginView', $data); */
        /* $this->addEventListener(AbstractController::TRIGGER_AFTER_ACTION, function() { */
        /*     $this->exChange(DashboardController::class); */
        /* }); */
    }    
}

