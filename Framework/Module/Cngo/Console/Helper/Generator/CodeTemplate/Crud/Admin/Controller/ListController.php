<?php

namespace Framework\Module\{Module}\Controller{Namespace};

use Framework\Module\Cngo\Admin\Controller\AbstractAdminController;
use Framework\ViewModel\ViewModelManager;
use Framework\Module\{Module}\View\ViewModel{Namespace}\ListViewModel;
use Framework\Repository\EntityManagerAwareInterface;
use Framework\Module\{Module}\Entity\{Entity};

class ListController extends AbstractAdminController implements EntityManagerAwareInterface
{
    use \Framework\Repository\EntityManagerAwareTrait;

    public function index()
    {
        return ViewModelManager::getViewModel([
            'viewModel' => ListViewModel::class,
            'data' => [
                '{entity}' => $this->getEntityManager()->getRepository({Entity}::class)->findBy([
                    'deleteFlag' => 0
                ], ['{entity}Id' => 'ASC'], 50),
            ]
        ]);
    }

    public static function getPageInfo()
    {
        return [
            'description' => '一覧',
            'priority' => 1,
            'menu' => true,
            'group' => '管理',
        ];
    }
}
