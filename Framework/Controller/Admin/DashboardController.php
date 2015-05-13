<?php

namespace Framework\Controller\Admin;

use Framework\Core\AbstractController;
use Framework\Core\ViewModel\ViewModelManager;

class DashboardController extends AbstractController
{
    
    public function index()
    {
        return ViewModelManager::getViewModel([
            "viewModel" => "IndexViewModel",
        ]);
    }

}