<?php

namespace Framework\ViewModel\Admin\Component;

use Framework\ViewModel\ViewModel\AbstractViewModel;

class TableTreeViewModel extends AbstractViewModel
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

    private $head = null;

    public function setHead ($head)
    {
        return $this->head = $head;
    }

    public function getHead ()
    {
        if($this->head === null) {
            $this->head = $this->getConfig()['head'];
        }
        return $this->head;
    }    
}
