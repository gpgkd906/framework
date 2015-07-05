<?php



class Collection
{
    /**
     *
     * @api
     * @var mixed $set 
     * @access private
     * @link
     */
    private $set = [];

    /**
     * 
     * @api
     * @param mixed $set
     * @return mixed $set
     * @link
     */
    public function setSet ($set)
    {
        return $this->set = $set;
    }

    /**
     * 
     * @api
     * @return mixed $set
     * @link
     */
    public function getSet ()
    {
        return $this->set;
    }

    public function addSet($id, $row)
    {
        $this->set[$id] = $row;
    }
    
    public function addTemplateElement($element)
    {    
        if(empty($element)) {
            return false;
        }
        $id = $element["attrs"]["id"];
        $content = $element["content"];
        $this->addSet($id, $element);
        if(empty($element["child"])) {
            return false;
        }
        foreach($element["child"] as $child) {
            $this->addTemplateElement($child);
        }
    }

    /**
     * 
     * @api
     * @param   
     * @param    
     * @return
     * @link
     */
    public function each ($callback)
    {
        if(!is_callable($callback)) {
            throw new Exception("invalid callback");
        }
        foreach($this->getSet() as $id => $set) {
            call_user_func($callback, $id, $set);
        }
    }
}