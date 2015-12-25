<?php

namespace Framework\Controller\Admin;

use Framework\Controller\Controller\AbstractController;
use Framework\ViewModel\ViewModel\ViewModelManager;
use Framework\Event\Event\EventManager;

class DashboardController extends AbstractController
{    
    public function index()
    {
        return ViewModelManager::getViewModel([
            "viewModel" => "Admin\DashboardViewModel",
        ]);
    }

}
