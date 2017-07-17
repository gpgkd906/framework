<?php
namespace Framework\Module\Cngo\Admin\Controller;

use Framework\Controller\AbstractController;
use Framework\ViewModel\ViewModel\ViewModelManager;
use Framework\Event\Event\EventManager;
use Framework\Module\Cngo\Admin\View\ViewModel\DashboardViewModel;
use Framework\Module\Cngo\Admin\Entity\User;
use Framework\Service\CacheService\CacheServiceAwareInterface;

class DashboardController extends AbstractController implements CacheServiceAwareInterface
{
    use \Framework\Service\CacheService\CacheServiceAwareTrait;

    public function index()
    {
        return ViewModelManager::getViewModel([
            "viewModel" => DashboardViewModel::class,
        ]);
    }
}
