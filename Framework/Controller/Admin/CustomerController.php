<?php

namespace Framework\Controller\Admin;

use Framework\Controller\Controller\ControllerInterface;
use Framework\Controller\Controller\AbstractController;
use Framework\ViewModel\ViewModel\ViewModelManager;
use Framework\Event\Event\EventTargetInterface;
    
class CustomerController extends AbstractController implements ControllerInterface, EventTargetInterface
{
    use \Framework\Event\Event\EventTargetTrait;
    use \Framework\Event\Event\TestTrait;
        
    private $test = [1, 2, 3];
    
    const TRIGGER_TEST = 'test';
    const TRIGGER_OPTION = [123, 4556];
    
    public function index($id = null, $length = null)
    {
        $Session = $this->getServiceManager()->getSessionService();
        return $ViewModelManager->getViewModel([
            'viewModel' => 'Admin\CustomerViewModel',
        ]);
    }
}
