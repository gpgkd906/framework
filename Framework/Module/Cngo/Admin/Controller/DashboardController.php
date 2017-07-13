<?php
namespace Framework\Module\Cngo\Admin\Controller;

use Framework\Controller\AbstractController;
use Framework\ViewModel\ViewModel\ViewModelManager;
use Framework\Event\Event\EventManager;
use Framework\Module\Cngo\Admin\View\ViewModel\DashboardViewModel;
use Framework\Module\Cngo\Admin\Entity\User;

class DashboardController extends AbstractController
{
    public function index()
    {
        $EntityManager = $this->getObjectManager()->get('EntityManager');
        $UserRepository = $EntityManager->getRepository(User::class);

        return ViewModelManager::getViewModel([
            "viewModel" => DashboardViewModel::class,
        ]);
    }
}
