<?php

namespace Framework\Controller\Admin;

use Framework\Controller\Controller\ControllerInterface;
use Framework\Controller\Controller\AbstractController;
use Framework\ViewModel\ViewModel\ViewModelManager;
use Framework\Event\EventManager\EventTargetInterface;

class CustomerController extends AbstractController implements ControllerInterface, EventTargetInterface
{
    use \Framework\Event\EventManager\EventTargetTrait;

    public function index($id = null, $length = null)
    {
        $Session = $this->getObjectManager()->getSessionService();
        return ViewModelManager::getViewModel([
            'viewModel' => 'Admin\CustomerViewModel',
        ]);
    }
}
