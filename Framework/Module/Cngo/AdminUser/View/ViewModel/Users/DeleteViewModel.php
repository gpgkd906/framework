<?php
declare(strict_types=1);

namespace Framework\Module\Cngo\AdminUser\View\ViewModel\Users;

use Framework\ViewModel\FormViewModel;
use Framework\Module\Cngo\Admin\View\Layout\AdminPageLayout;
class DeleteViewModel extends FormViewModel
{
    protected $template = '/template/users/delete.phtml';

    protected $useConfirm = false;

    protected $config = [
        'layout' => AdminPageLayout::class,
    ];

    public $listeners = [
        'Render' => 'onRender',
    ];

    public function getTemplateDir()
    {
        return __DIR__ . '/../..';
    }

    public function onRender()
    {
        $data = $this->getData();
        $data['adminUser'] = $data['adminUser']->toArray();
        $this->setData($data);
        $form = $this->getForm();
        $form->submit->set('value', '削除する');
        $form->submit->removeClass('btn-success')->addClass('btn-danger');
    }
}
