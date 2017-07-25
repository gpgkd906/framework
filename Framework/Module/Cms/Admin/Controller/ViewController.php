<?php

namespace Framework\Module\Cngo\AdminUser\Controller\Cms;

use Framework\Controller\AbstractController;
use Framework\ViewModel\ViewModelManager;
use Framework\Module\Cngo\Admin\Controller\AbstractAdminController;

class ViewController extends AbstractAdminController
{
    public function index()
    {
        return ViewModelManager::getViewModel([
            'viewModel' => 'Admin\Cms\ViewListViewModel',
        ]);
    }
}
