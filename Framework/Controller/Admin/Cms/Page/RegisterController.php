<?php

namespace Framework\Controller\Admin\Cms\Page;

use Framework\Controller\Controller\AbstractController;
use Framework\ViewModel\ViewModel\ViewModelManager;
use Framework\Model\Cms\PageModel;
use Framework\ViewModel\Admin\Cms\PageRegisterViewModel;

class RegisterController extends AbstractController
{    
    private $pageModel = null;

    public function index()
    {
        $param = $this->getParam();
        $this->getPageModel()->setIdentify($param['pid']);
        return ViewModelManager::getViewModel([
            'viewModel' => PageRegisterViewModel::class,
            'listeners' => [
                'Complete' => [$this, 'onRegisterComplete']
            ],
            'model' => $this->getPageModel(),
        ]);
    }

    public function onRegisterComplete($formViewModel, $data)
    {
        $this->getPageModel()->saveNewPage($data['page']);
    }

    private function setPageModel ($pageModel)
    {
        return $this->pageModel = $pageModel;
    }

    private function getPageModel ()
    {
        if($this->pageModel === null) {
            $this->pageModel = $this->getServiceManager()->get('Model', 'Cms\PageModel');
        }
        return $this->pageModel;
    }
}
