<?php

namespace Framework\ViewModel\Admin\Component;

use Framework\ViewModel\Admin\Component\TableViewModel;

class TableTreeViewModel extends TableViewModel
{
    protected $template = '/template/admin/component/table_tree.html';    

    protected $config = [
        'script' => [
            '/js/jquery.treegrid.min.js',
            '/js/jquery.treegrid.bootstrap3.js',
            '/js/tableTree.js',
        ],
        'style' => [
            '/css/jquery.treegrid.css'
        ],
    ];
}
