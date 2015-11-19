<?php

namespace Framework\ViewModel\Admin\Cms\Page;

use Framework\ViewModel\ViewModel\AbstractViewModel;
use Framework\ViewModel\Admin\Component\TableTreeViewModel;
use Framework\Model\Cms\PageModel;

class ListViewModel extends AbstractViewModel
{    
    protected $template = '/template/admin/cms/page/list.html';

    protected $config = [
        'model' => PageModel::class,
        'container' => [
            'PageTableTree' => [
                [
                    'viewModel' => TableTreeViewModel::class,
                    'id' => 'PageTableTree',
                    'head' => [
                        'name', 'controller',
                    ]
                ],
            ]
        ],        
    ];

    public $listeners = [
        self::TRIGGER_RENDER => 'onRender',
    ];

    public function onRender()
    {
        $this->getChild('PageTableTree')->setData($this->getData());
    }
    
    public function getData()
    {
        //var_dump($this->getChild('Table'));
        $list = parent::getData();
        $entities = [];
        foreach($list as $row) {
            $dir = $row['dir'];
            if(!isset($entities[$dir])) {
                $entities[$dir] = [];
            }
            $entities[$dir][] = $row;
        }
        ksort($entities);
        return $entities;
    }
}