<?php

namespace Framework\Controller\Admin;

use Framework\Controller\Controller\AbstractController;
use Framework\ViewModel\ViewModel\ViewModelManager;

class DashboardController extends AbstractController
{
    
    public function index()
    {
        var_dump($this->getServiceManager()->getModel('Book'));
        return ViewModelManager::getViewModel([
            "viewModel" => "IndexViewModel",
        ]);
    }

}
