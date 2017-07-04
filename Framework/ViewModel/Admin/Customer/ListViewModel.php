<?php

namespace Framework\ViewModel\Admin\Customer;

use Framework\ViewModel\ViewModel\AbstractViewModel;
use Framework\Model\Customer\ListModel;

class ListViewModel extends AbstractViewModel
{    
    protected $template = '/template/admin/customer/list.html';
    
    protected $config = [
        'model' => ListModel::class,
    ];    
}