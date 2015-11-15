<?php

namespace Framework\ViewModel\Admin\Cms\Page;

use Framework\ViewModel\ViewModel\AbstractViewModel;
use Framework\Model\Cms\PageModel;

class ListViewModel extends AbstractViewModel
{    
    protected $template = '/template/admin/cms/page/list.html';

    protected $config = [
        'model' => PageModel::class,
    ];    
}