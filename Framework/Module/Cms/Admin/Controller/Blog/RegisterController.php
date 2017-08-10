<?php
declare(strict_types=1);

namespace Framework\Module\Cms\Admin\Controller\Blog;

use Framework\Module\Cngo\Admin\Controller\AbstractAdminController;
use Framework\ViewModel\ViewModelManager;
use Framework\Module\Cms\Admin\View\ViewModel\Blog\RegisterViewModel;
use Framework\Repository\EntityManagerAwareInterface;
use Framework\Module\Cms\Admin\Entity\Blog;

class RegisterController extends AbstractAdminController implements EntityManagerAwareInterface
{
    use \Framework\Repository\EntityManagerAwareTrait;

    public function index()
    {
        return ViewModelManager::getViewModel([
          'viewModel' => RegisterViewModel::class,
          'listeners' => [
              RegisterViewModel::TRIGGER_FORMCOMPLETE => [$this, 'onRegisterComplete']
          ]
        ]);
    }

    public function onRegisterComplete(\Framework\EventManager\Event $event)
    {
        $ViewModel = $event->getTarget();
        if ($ViewModel->getForm()->isValid()) {
            $blog = $ViewModel->getForm()->getData()['blog'];
            $AdminUser = new Blog();
            $AdminUser->fromArray($blog);
            $this->getEntityManager()->persist($AdminUser);
            $this->getEntityManager()->flush();
            $this->getRouter()->redirect(ListController::class);
        }
    }

    public static function getPageInfo()
    {
        return [
            'description' => '博客登録',
            'priority' => 2,
            'menu' => true,
            'group' => '博客',
        ];
    }
}
