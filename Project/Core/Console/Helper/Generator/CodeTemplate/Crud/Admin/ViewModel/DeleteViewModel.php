<?php
/**
 * PHP version 7
 * File DeleteViewModel.php
 *
 * @category ViewModel
 * @package  Framework\Module\{Module}
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
declare(strict_types=1);

namespace Framework\Module\{Module}\View\ViewModel{Namespace};

use Std\ViewModel\FormViewModel;
use Project\Core\Admin\View\Layout\AdminPageLayout;

/**
 * Class DeleteViewModel
 *
 * @category ViewModel
 * @package  Framework\Module\{Module}
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
class DeleteViewModel extends FormViewModel
{
    protected $template = '/template{namespace}/delete.phtml';

    protected $useConfirm = false;

    protected $config = [
        'layout' => AdminPageLayout::class,
    ];

    public $listeners = [
        self::TRIGGER_BEFORE_RENDER => 'onRender',
    ];

    /**
     * Method GetTemplateDir
     *
     * @return string templateDir
     */
    public function getTemplateDir(): string
    {
        return __DIR__ . '/..{ns}';
    }

    /**
     * Method onRender
     *
     * @return void
     */
    public function onRender(): void
    {
        $data = $this->getData();
        $data['{entity}'] = $data['{entity}']->toArray();
        $this->setData($data);
        $form = $this->getForm();
        $form->submit->set('value', '削除する');
        $form->submit->removeClass('btn-success')->addClass('btn-danger');
    }
}
