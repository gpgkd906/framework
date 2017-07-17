<?php
namespace Framework\Module\Cngo\Admin\Controller;

use Framework\ViewModel\ViewModel\ViewModelManager;
use Framework\Module\Cngo\Admin\View\ViewModel\DashboardViewModel;
use Framework\Service\CacheService\CacheServiceAwareInterface;
use Framework\Module\Cngo\Admin\Controller\AbstractAdminController;

class DashboardController extends AbstractAdminController implements CacheServiceAwareInterface
{
    use \Framework\Service\CacheService\CacheServiceAwareTrait;

    public function index()
    {
        return ViewModelManager::getViewModel([
            "viewModel" => DashboardViewModel::class,
        ]);
    }
}
