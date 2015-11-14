<?php

namespace Framework\Controller\Admin\Cms;

use Framework\Controller\Controller\AbstractController;
use Framework\ViewModel\ViewModel\ViewModelManager;
use Framework\Model\Cms\PageModel;

class PageController extends AbstractController
{
    public function index()
    {
        var_dump(

            $this->getServiceManager()->getComponent('Model', 'Cms\Page')
        );
        
        return ViewModelManager::getViewModel([
            'viewModel' => 'Admin\Cms\PageListViewModel',
            //'model' => new PageModel,
        ]);
    }
}
