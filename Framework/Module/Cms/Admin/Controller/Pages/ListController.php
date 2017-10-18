<?php
/**
 * PHP version 7
 * File ListController.php
 *
 * @category Controller
 * @package  Framework\Module\{Module}
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
declare(strict_types=1);

namespace Framework\Module\{Module}\Controller\Pages;

use Framework\Module\Cngo\Admin\Controller\AbstractAdminController;
use Framework\ViewModel\ViewModelManager;
use Framework\Module\{Module}\View\ViewModel\Pages\ListViewModel;
use Framework\Repository\EntityManagerAwareInterface;
use Framework\Module\{Module}\Entity\Pages;

/**
 * Class ListController
 *
 * @category Controller
 * @package  Framework\Module\{Module}
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
class ListController extends AbstractAdminController implements EntityManagerAwareInterface
{
    use \Framework\Repository\EntityManagerAwareTrait;

    /**
     * Method index
     *
     * @return ListViewModel
     */
    public function index(): ListViewModel
    {
        return $this->getViewModelManager()->getViewModel([
            'viewModel' => ListViewModel::class,
            'data' => [
                'pages' => $this->getEntityManager()->getRepository(Pages::class)->findBy([
                    'deleteFlag' => 0
                ], ['pagesId' => 'ASC'], 50),
            ]
        ]);
    }

    /**
     * Method getPageInfo
     *
     * @return Array
     */
    public static function getPageInfo(): array
    {
        return [
            'description' => '一覧',
            'priority' => 1,
            'menu' => true,
            'icon' => '<i class="mdi mdi-av-timer fa-fw" data-icon="v"></i>',
            'group' => '管理',
            'groupIcon' => '<i class="mdi mdi-av-timer fa-fw" data-icon="v"></i>',
        ];
    }
}
