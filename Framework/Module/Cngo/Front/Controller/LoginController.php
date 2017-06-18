<?php

namespace Framework\Controller\Login;

use Framework\Controller\Controller\AbstractController;
use Framework\ViewModel\ViewModel\ViewModelManager;

class LoginController extends AbstractController
{
    
    public function index()
    {
        $viewModel = ViewModelManager::getViewModel([
            "viewModel" => "Login",
            "Model" => "Users"
        ]);
        return $viewModel;
    }

}
