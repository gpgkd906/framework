<?php

namespace Framework\ViewModel\Admin\Component;

use Framework\ViewModel\ViewModel\AbstractViewModel;

class TableViewModel extends AbstractViewModel
{
    protected $template = '/template/admin/component/table.html';

    private $head = null;

    public function setHead ($head)
    {
        return $this->head = $head;
    }
    
    public function getHead ()
    {
        if ($this->head === null) {
            $this->head = $this->getConfig()['head'];
        }
        return $this->head;
    }

    /**
     *
     * @api
     * @var mixed $section 
     * @access private
     * @link
     */
    private $section = null;

    /**
     * 
     * @api
     * @param mixed $section
     * @return mixed $section
     * @link
     */
    public function setSection ($section)
    {
        return $this->section = $section;
    }

    /**
     * 
     * @api
     * @return mixed $section
     * @link
     */
    public function getSection ()
    {
        return $this->section;
    }
}
