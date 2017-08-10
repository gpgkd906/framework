<?php
declare(strict_types=1);

namespace Framework\Module\Cms\Admin\Controller\Page;

use Framework\Module\Cngo\Admin\Controller\AbstractAdminController;
use Framework\ViewModel\ViewModelManager;
use Framework\Module\Cms\Admin\View\ViewModel\Page\ListViewModel;
use Framework\Repository\EntityManagerAwareInterface;
use Framework\Module\Cms\Admin\Entity\Controller;

class ListController extends AbstractAdminController implements EntityManagerAwareInterface
{
    use \Framework\Repository\EntityManagerAwareTrait;

    public function index()
    {
        return ViewModelManager::getViewModel([
            'viewModel' => ListViewModel::class,
            'data' => [
                'controller' => $this->getEntityManager()->getRepository(Controller::class)->findBy([
                    'deleteFlag' => 0
                ], ['controllerId' => 'ASC'], 50),
            ]
        ]);
    }

    public static function getPageInfo()
    {
        return [
            'description' => '页面信息一覧',
            'priority' => 1,
            'menu' => true,
            'group' => '页面信息管理',
        ];
    }
}
