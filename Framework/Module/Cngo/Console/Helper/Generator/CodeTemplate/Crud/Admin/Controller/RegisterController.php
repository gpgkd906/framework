<?php
/**
 * PHP version 7
 * File RegisterController.php
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
use Framework\Module\{Module}\View\ViewModel{Namespace}\RegisterViewModel;
use Framework\Repository\EntityManagerAwareInterface;
use Framework\Module\{Module}\Entity\{Entity};

/**
 * Class RegisterController
 *
 * @category Controller
 * @package  Framework\Module\{Module}
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
class RegisterController extends AbstractAdminController implements EntityManagerAwareInterface
{
    use \Framework\Repository\EntityManagerAwareTrait;

    /**
     * Method index
     *
     * @return RegisterViewModel
     */
    public function index(): RegisterViewModel
    {
        return ViewModelManager::getViewModel(
            [
                'viewModel' => RegisterViewModel::class,
                'listeners' => [
                    RegisterViewModel::TRIGGER_FORMCOMPLETE => [$this, 'onRegisterComplete']
                ]
            ]
        );
    }

    /**
     * Method onRegisterComplete
     *
     * @param \Framework\EventManager\Event $event 'Event'
     *
     * @return void
     */
    public function onRegisterComplete(\Framework\EventManager\Event $event): void
    {
        $ViewModel = $event->getTarget();
        if ($ViewModel->getForm()->isValid()) {
            ${entity} = $ViewModel->getForm()->getData()['{entity}'];
            $AdminUser = new {Entity}();
            $AdminUser->fromArray(${entity});
            $this->getEntityManager()->persist($AdminUser);
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
            'description' => '登録',
            'priority' => 2,
            'menu' => true,
            'icon' => '<i class="mdi mdi-av-timer fa-fw" data-icon="v"></i>',
            'group' => '管理',
            'groupIcon' => '<i class="mdi mdi-av-timer fa-fw" data-icon="v"></i>',
        ];
    }
}
