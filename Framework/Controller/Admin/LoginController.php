<?php

namespace Framework\Controller\Admin;

use Framework\Controller\Controller\AbstractController;
use Framework\ViewModel\ViewModel\ViewModelManager;

class LoginController extends AbstractController
{
    
    public function index()
    {
        return ViewModelManager::getViewModel([
            "viewModel" => "Admin\LoginViewModel",
        ]);
    }

}
