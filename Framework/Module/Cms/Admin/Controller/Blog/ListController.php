<?php
declare(strict_types=1);

namespace Framework\Module\Cms\Admin\Controller\Blog;

use Framework\Module\Cngo\Admin\Controller\AbstractAdminController;
use Framework\ViewModel\ViewModelManager;
use Framework\Module\Cms\Admin\View\ViewModel\Blog\ListViewModel;
use Framework\Repository\EntityManagerAwareInterface;
use Framework\Module\Cms\Admin\Entity\Blog;




class ListController extends AbstractAdminController implements EntityManagerAwareInterface
{
    use \Framework\Repository\EntityManagerAwareTrait;

    public function index()
    {
        return ViewModelManager::getViewModel([
            'viewModel' => ListViewModel::class,
            'data' => [
                'blog' => $this->getEntityManager()->getRepository(Blog::class)->findBy([
                    'deleteFlag' => 0
                ], ['blogId' => 'ASC'], 50),
            ]
        ]);
    }

    public static function getPageInfo()
    {
        return [
            'description' => '博客一覧',
            'priority' => 1,
            'menu' => true,
            'group' => '博客',
        ];
    }
}
