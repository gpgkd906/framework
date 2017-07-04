<?php

namespace Framework\Module\Cngo\Admin\Controller;

use Framework\Controller\Controller\ControllerInterface;
use Framework\Controller\Controller\AbstractController;
use Framework\ViewModel\ViewModel\ViewModelManager;
use Framework\EventManager\EventTargetInterface;

class CustomerController extends AbstractController implements ControllerInterface, EventTargetInterface
{
    use \Framework\EventManager\EventTargetTrait;

    public function index($id = null, $length = null)
    {
        $Session = $this->getObjectManager()->getSessionService();
        return ViewModelManager::getViewModel([
            'viewModel' => 'Admin\CustomerViewModel',
        ]);
    }
}
