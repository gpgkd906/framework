<?php
declare(strict_types=1);

namespace Framework\Module\Cms\Admin\Controller\Section;

use Framework\Module\Cngo\Admin\Controller\AbstractAdminController;
use Framework\ViewModel\ViewModelManager;
use Framework\Module\Cms\Admin\View\ViewModel\Section\ListViewModel;
use Framework\Repository\EntityManagerAwareInterface;
use Framework\Module\Cms\Admin\Entity\ControllerGroup;

class ListController extends AbstractAdminController implements EntityManagerAwareInterface
{
    use \Framework\Repository\EntityManagerAwareTrait;

    public function index()
    {
        return ViewModelManager::getViewModel([
            'viewModel' => ListViewModel::class,
            'data' => [
                'controllerGroup' => $this->getEntityManager()->getRepository(ControllerGroup::class)->findBy([
                    'deleteFlag' => 0
                ], ['controllerGroupId' => 'ASC'], 50),
            ]
        ]);
    }

    public static function getPageInfo()
    {
        return [
            'description' => '页面分组一覧',
            'priority' => 1,
            'menu' => true,
            'group' => '页面分组管理',
        ];
    }
}
