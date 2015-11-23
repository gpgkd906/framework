<?php

namespace Framework\ViewModel\Admin\Cms\Page;

use Framework\ViewModel\ViewModel\SubFormViewModel;
use Framework\Fieldset\Admin\PageFieldset;

class RegisterFormViewModel extends SubFormViewModel
{
    protected $template = '/template/admin/cms/page/form.html';

    private $id = 'cms_page_register';
    
    protected $fieldset = PageFieldset::class;    
}