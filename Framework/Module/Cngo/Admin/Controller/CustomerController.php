<?php

namespace Framework\Module\Cngo\Admin\Controller;

use Framework\Controller\ControllerInterface;
use Framework\Controller\AbstractController;
use Framework\ViewModel\ViewModel\ViewModelManager;
use Framework\EventManager\EventTargetInterface;
use Framework\Module\Cngo\Admin\Controller\AbstractAdminController;

class CustomerController extends AbstractAdminController
{
    public function index($id = null, $length = null)
    {
        return ViewModelManager::getViewModel([
            'viewModel' => 'Admin\CustomerViewModel',
        ]);
    }
}
