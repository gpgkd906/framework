<?php
/**
 * PHP version 7
 * File DeleteController.php
 *
 * @category Controller
 * @package  Framework\Module\{Module}
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
declare(strict_types=1);

namespace Framework\Module\{Module}\Controller{Namespace};

use Framework\Module\Cngo\Admin\Controller\AbstractAdminController;
use Framework\ViewModel\ViewModelManager;
use Framework\Module\{Module}\View\ViewModel{Namespace}\DeleteViewModel;
use Framework\Repository\EntityManagerAwareInterface;
use Framework\Module\{Module}\Entity\{Entity};

/**
 * Class DeleteController
 *
 * @category Controller
 * @package  Framework\Module\{Module}
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
class DeleteController extends AbstractAdminController implements EntityManagerAwareInterface
{
    use \Framework\Repository\EntityManagerAwareTrait;
    private ${Entity};

    /**
     * Method index
     *
     * @param integer|str $id EntityId
     *
     * @return DeleteViewModel
     */
    public function index($id): DeleteViewModel
    {
        $this->{Entity} = $this->getEntityManager()->getRepository({Entity}::class)->find($id);
        if (!$this->{Entity}) {
            $this->getRouter()->redirect(ListController::class);
        }
        return $this->getViewModelManager()->getViewModel([
            'viewModel' => DeleteViewModel::class,
            'data' => [
                '{entity}' => $this->{Entity},
            ],
            'listeners' => [
                DeleteViewModel::TRIGGER_FORMCOMPLETE => [$this, 'onDeleteComplete']
            ],
        ]);
    }

    /**
     * Method onDeleteComplete
     *
     * @param \Framework\EventManager\Event $event 'Event'
     *
     * @return void
     */
    public function onDeleteComplete(\Framework\EventManager\Event $event): void
    {
        $ViewModel = $event->getTarget();
        if ($ViewModel->getForm()->isValid()) {
            ${Entity} = $this->{Entity};
            ${Entity}->setDeleteFlag(true);
            $this->getEntityManager()->merge(${Entity});
            $this->getEntityManager()->flush();
            $this->getRouter()->redirect(ListController::class);
        }
    }

    /**
     * Method getPageInfo
     *
     * @return Array
     */
    public static function getPageInfo(): array
    {
        return [
            'description' => '削除',
            'priority' => 0,
            'menu' => false,
        ];
    }
}
