<?php

namespace Framework\Module\Cngo\Admin\Controller;

use Framework\Controller\ControllerInterface;
use Framework\Controller\AbstractController;
use Framework\ViewModel\ViewModel\ViewModelManager;
use Framework\EventManager\EventTargetInterface;

class CustomerController extends AbstractController implements ControllerInterface, EventTargetInterface
{
    use \Framework\EventManager\EventTargetTrait;

    public function index($id = null, $length = null)
    {
        return ViewModelManager::getViewModel([
            'viewModel' => 'Admin\CustomerViewModel',
        ]);
    }
}
