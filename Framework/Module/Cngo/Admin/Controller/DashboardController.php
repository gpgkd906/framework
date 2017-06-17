<?php
namespace Framework\Module\Cngo\Admin\Controller;

use Framework\Controller\Controller\AbstractController;
use Framework\ViewModel\ViewModel\ViewModelManager;
use Framework\Event\Event\EventManager;
use Framework\Module\Cngo\Admin\View\ViewModel\DashboardViewModel;

class DashboardController extends AbstractController
{
    public function index()
    {
        return ViewModelManager::getViewModel([
            "viewModel" => DashboardViewModel::class,
        ]);
    }
}
