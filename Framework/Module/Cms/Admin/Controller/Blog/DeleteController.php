<?php
declare(strict_types=1);

namespace Framework\Module\Cms\Admin\Controller\Blog;

use Framework\Module\Cngo\Admin\Controller\AbstractAdminController;
use Framework\ViewModel\ViewModelManager;
use Framework\Module\Cms\Admin\View\ViewModel\Blog\DeleteViewModel;
use Framework\Repository\EntityManagerAwareInterface;
use Framework\Module\Cms\Admin\Entity\Blog;

class DeleteController extends AbstractAdminController implements EntityManagerAwareInterface
{
    use \Framework\Repository\EntityManagerAwareTrait;
    private $Blog;

    public function index($id)
    {
        $this->Blog = $this->getEntityManager()->getRepository(Blog::class)->find($id);
        if (!$this->Blog) {
            $this->getRouter()->redirect(ListController::class);
        }
        return ViewModelManager::getViewModel([
            'viewModel' => DeleteViewModel::class,
            'data' => [
                'blog' => $this->Blog,
            ],
            'listeners' => [
                DeleteViewModel::TRIGGER_FORMCOMPLETE => [$this, 'onDeleteComplete']
            ],
        ]);
    }

    public function onDeleteComplete(\Framework\EventManager\Event $event)
    {
        $ViewModel = $event->getTarget();
        if ($ViewModel->getForm()->isValid()) {
            $Blog = $this->Blog;
            $Blog->setDeleteFlag(true);
            $this->getEntityManager()->merge($Blog);
            $this->getEntityManager()->flush();
            $this->getRouter()->redirect(ListController::class);
        }
    }

    public static function getPageInfo()
    {
        return [
            "description" => "博客削除",
            "priority" => 0,
            "menu" => false
        ];
    }
}
