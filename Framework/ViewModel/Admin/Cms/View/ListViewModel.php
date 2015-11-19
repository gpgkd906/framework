<?php

namespace Framework\ViewModel\Admin\Cms\View;

use Framework\ViewModel\ViewModel\AbstractViewModel;
use Framework\Model\Cms\ViewModel;

class ListViewModel extends AbstractViewModel
{    
    protected $template = '/template/admin/cms/view/list.html';

    protected $config = [
        'model' => ViewModel::class,
    ];    
}