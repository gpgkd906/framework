<?php

namespace Framework\Module\Cngo\Admin\Controller\Cms;

use Framework\Controller\Controller\AbstractController;
use Framework\ViewModel\ViewModel\ViewModelManager;

class ViewController extends AbstractController
{
    public function index()
    {
        return ViewModelManager::getViewModel([
            'viewModel' => 'Admin\Cms\ViewListViewModel',
        ]);
    } 
}
