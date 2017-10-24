<?php
declare(strict_types=1);
namespace Project\Core\Admin\Controller;

use Std\ViewModel\ViewModelManager;
use Project\Core\Admin\View\ViewModel\DashboardViewModel;
use Std\CacheManager\CacheManagerAwareInterface;
use Project\Core\Admin\Controller\AbstractAdminController;
use Std\SessionManager\SessionManagerAwareInterface;

class DashboardController extends AbstractAdminController implements
    SessionManagerAwareInterface,
    CacheManagerAwareInterface
{
    use \Std\CacheManager\CacheManagerAwareTrait;
    use \Std\SessionManager\SessionManagerAwareTrait;

    public function index()
    {
        return $this->getViewModelManager()->getViewModel([
            "viewModel" => DashboardViewModel::class,
        ]);
    }

    public static function getPageInfo()
    {
        return [
            "description" => "Dashboard",
            "priority" => 0,
            "menu" => true,
            "icon" => '<i class="mdi mdi-av-timer fa-fw" data-icon="v"></i>'
        ];
    }
}
