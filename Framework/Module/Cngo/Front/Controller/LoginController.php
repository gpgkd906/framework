<?php
declare(strict_types=1);

namespace Framework\Controller\Login;

use Framework\Controller\AbstractController;
use Framework\ViewModel\ViewModelManager;

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
