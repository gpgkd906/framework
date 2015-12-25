<?php

namespace Framework\Controller\Admin;

use Framework\Controller\Controller\AbstractController;
use Framework\ViewModel\ViewModel\ViewModelManager;
use Framework\Event\Event\EventManager;
use Framework\Event\Event\EventInterface;

class DashboardController extends AbstractController
{
    const TRIGGER_TEST = 'test';
    private $test = false;
    
    public function index()
    {
        return ViewModelManager::getViewModel([
            "viewModel" => "Admin\DashboardViewModel",
        ]);
    }

}
