<?php

namespace Framework\Module\Cngo\Admin\Controller\Cms;

use Framework\Controller\AbstractController;
use Framework\ViewModel\ViewModel\ViewModelManager;
use Framework\Module\Cngo\Admin\Controller\AbstractAdminController;

class PageController extends AbstractAdminController
{
    public function index()
    {
        return ViewModelManager::getViewModel([
            'viewModel' => 'Admin\Cms\PageListViewModel',
        ]);
    }
}
