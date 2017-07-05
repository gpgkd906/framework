<?php

namespace Framework\ViewModel\Admin\Cms\Page;

use Framework\ViewModel\ViewModel\SubFormViewModel;
use Framework\Fieldset\Admin\PageFieldset;

class RegisterFormViewModel extends SubFormViewModel
{
    protected $template = '/template/admin/cms/page/form.html';

    private $id = 'cms_page_register';
    
    protected $fieldset = PageFieldset::class;
    
    public $listeners = [
        self::TRIGGER_FORMINIT => 'onFormInit',
    ];

    public function onFormInit()
    {
        $fieldset = $this->getForm()->getFieldset('page');        
        $entity = $this->getModel()->getEntity();
        if ($entity) {
            $fieldset->get('model')->val(['新規作成' => 0 ] + $entity['options']['model'] ?? []);
            $fieldset->get('view')->val(['新規作成' => 0 ] + $entity['options']['viewModel'] ?? []);
        }
        $fieldset->bind($entity);
    }
}