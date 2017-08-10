<?php
declare(strict_types=1);
namespace Framework\Module\Cngo\Admin\Controller;

use Framework\ViewModel\ViewModelManager;
use Framework\Module\Cngo\Admin\View\ViewModel\DashboardViewModel;
use Framework\Service\CacheService\CacheServiceAwareInterface;
use Framework\Module\Cngo\Admin\Controller\AbstractAdminController;
use Framework\Service\SessionService\SessionServiceAwareInterface;

class DashboardController extends AbstractAdminController implements
    SessionServiceAwareInterface,
    CacheServiceAwareInterface
{
    use \Framework\Service\CacheService\CacheServiceAwareTrait;
    use \Framework\Service\SessionService\SessionServiceAwareTrait;

    public function index()
    {
        return ViewModelManager::getViewModel([
            "viewModel" => DashboardViewModel::class,
        ]);
    }

    public static function getPageInfo()
    {
        return [
            "description" => "ダッシュボード",
            "priority" => 0,
            "menu" => true
        ];
    }
}
