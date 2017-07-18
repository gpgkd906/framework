<?php
namespace Framework\Module\Cngo\Admin\Controller;

use Framework\ViewModel\ViewModel\ViewModelManager;
use Framework\Module\Cngo\Admin\View\ViewModel\DashboardViewModel;
use Framework\Service\CacheService\CacheServiceAwareInterface;
use Framework\Module\Cngo\Admin\Controller\AbstractAdminController;
use Framework\Repository\EntityManagerAwareInterface;
use Framework\Module\Cngo\Admin\Entity\AdminUsers;
use Framework\Service\SessionService\SessionServiceAwareInterface;

class DashboardController extends AbstractAdminController implements
    EntityManagerAwareInterface,
    SessionServiceAwareInterface,
    CacheServiceAwareInterface
{
    use \Framework\Repository\EntityManagerAwareTrait;
    use \Framework\Service\CacheService\CacheServiceAwareTrait;
    use \Framework\Service\SessionService\SessionServiceAwareTrait;

    public function index()
    {
        return ViewModelManager::getViewModel([
            "viewModel" => DashboardViewModel::class,
        ]);
    }
}
