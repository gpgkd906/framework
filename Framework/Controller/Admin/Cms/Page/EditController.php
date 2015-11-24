<?php

namespace Framework\Controller\Admin\Cms\Page;

use Framework\Controller\Controller\AbstractController;
use Framework\ViewModel\ViewModel\ViewModelManager;
use Framework\Model\Cms\PageModel;

class EditController extends AbstractController
{    
    public function index()
    {
        $param = $this->getParam();
        $PageModel = $this->getServiceManager()->get('Model', 'Cms\PageModel');
        $PageModel->setIdentify($param['pid']);
        return ViewModelManager::getViewModel([
            'viewModel' => 'Admin\Cms\PageEditViewModel',
            'listeners' => [
                'Complete' => [$this, 'onEditComplete'],
            ],
            'model' => $PageModel,
        ]);
    }

    public function onEditComplete($formViewModel, $data)
    {
        //var_dump($data, 'complete');
        /* $Session = $this->getServiceManager()->getSessionService(); */
        /* $Session->setSection('LoginView', $data); */
        /* $this->addEventListener(AbstractController::TRIGGER_AFTER_ACTION, function() { */
        /*     $this->exChange(DashboardController::class); */
        /* }); */
    }   
}
