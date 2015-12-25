<?php

namespace Framework\ViewModel\Admin\Component;

use Framework\ViewModel\ViewModel\AbstractViewModel;
use Framework\ViewModel\Admin\Component\ListItemViewModel;

class ListViewModel extends AbstractViewModel
{
    protected $template = '/template/admin/component/list.html';
    
    protected $config = [
        'itemView' = ''
    ];

    /**
     *
     * @api
     * @var mixed $itemView 
     * @access private
     * @link
     */
    protected $itemView = null;

    /**
     * 
     * @api
     * @param mixed $itemView
     * @return mixed $itemView
     * @link
     */
    public function setItemView ($itemView)
    {
        return $this->itemView = $itemView;
    }

    /**
     * 
     * @api
     * @return mixed $itemView
     * @link
     */
    public function getItemView ()
    {
        return $this->itemView;
    }
}