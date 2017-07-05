<?php

namespace Framework\Module\Cngo\Admin\Controller\Cms\Page;

use Framework\Controller\AbstractController;
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

    public function onRegisterComplete($event)
    {
        $data = $event->getData();
        $this->getPageModel()->saveNewPage($data['page']);
    }

    private function setPageModel ($pageModel)
    {
        return $this->pageModel = $pageModel;
    }

    private function getPageModel ()
    {
        if ($this->pageModel === null) {
            $this->pageModel = $this->getObjectManager()->get('Model', 'Cms\PageModel');
        }
        return $this->pageModel;
    }
}
