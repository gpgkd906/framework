<?php

namespace Framework\ViewModel\Admin\Cms\Page;

use Framework\ViewModel\ViewModel\SubFormViewModel;
use Framework\Fieldset\Admin\PageFieldset;

class EditFormViewModel extends SubFormViewModel
{
    protected $template = '/template/admin/cms/page/form.html';
    
    private $id = 'cms_page_edit';
    
    protected $fieldset = PageFieldset::class;
    
    public $listeners = [
        self::TRIGGER_FORMINIT => 'onFormInit',
    ];

    public function onFormInit()
    {
        $fieldset = $this->getForm()->getFieldset('page');
        $fieldset->get('url')->set('disabled', true);
        $entity = $this->getModel()->getEntity();
        $fieldset->bind($entity);
    }
}