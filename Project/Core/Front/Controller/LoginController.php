<?php
declare(strict_types=1);

namespace Std\Controller\Login;

use Std\Controller\AbstractController;
use Std\ViewModel\ViewModelManager;

class LoginController extends AbstractController
{

    public function index()
    {
        $viewModel = $this->getViewModelManager()->getViewModel([
            "viewModel" => "Login",
            "Model" => "Users"
        ]);
        return $viewModel;
    }

}
