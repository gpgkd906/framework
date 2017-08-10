<?php
declare(strict_types=1);

namespace Framework\Module\Cms\Admin\Controller\Blog;

use Framework\Module\Cngo\Admin\Controller\AbstractAdminController;
use Framework\ViewModel\ViewModelManager;
use Framework\Module\Cms\Admin\View\ViewModel\Blog\EditViewModel;
use Framework\Repository\EntityManagerAwareInterface;
use Framework\Module\Cms\Admin\Entity\Blog;

class EditController extends AbstractAdminController implements EntityManagerAwareInterface
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
            'viewModel' => EditViewModel::class,
            'data' => [
                'blog' => $this->Blog,
            ],
            'listeners' => [
                EditViewModel::TRIGGER_FORMCOMPLETE => [$this, 'onEditComplete']
            ],
        ]);
    }

    public function onEditComplete(\Framework\EventManager\Event $event)
    {
        $ViewModel = $event->getTarget();
        if ($ViewModel->getForm()->isValid()) {
            $blog = $ViewModel->getForm()->getData()['blog'];
            $Blog = $this->Blog;
            $Blog->fromArray($blog);
            $this->getEntityManager()->merge($Blog);
            $this->getEntityManager()->flush();
            $this->getRouter()->redirect(ListController::class);
        }
    }

    public static function getPageInfo()
    {
        return [
            "description" => "博客編集",
            "priority" => 0,
            "menu" => false
        ];
    }
}
