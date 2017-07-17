<?php

namespace Framework\Module\Cngo\Admin\Controller\Cms;

use Framework\Controller\AbstractController;
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
        /* $Cache = $this->getObjectManager()->getCacheService(); */
        /* $Cache->setSection('LoginView', $data); */
        /* $this->addEventListener(AbstractController::TRIGGER_AFTER_ACTION, function() { */
        /*     $this->exChange(DashboardController::class); */
        /* }); */
    }
}
