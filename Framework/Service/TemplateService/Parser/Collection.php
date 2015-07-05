<?php
namespace Framework\Service\TemplateService\Parser;
use Framework\Service\TemplateService\Parser\Interfaces\TagInterface;
use Exception;

class Collection
{
    /**
     *
     * @api
     * @var mixed $collection 
     * @access private
     * @link
     */
    private $collection = [];

    /**
     * 
     * @api
     * @return mixed $collection
     * @link
     */
    public function getCollection ()
    {
        return $this->collection;
    }

    public function addCollection($id, TagInterface $Tag)
    {
        $this->collection[$id] = $Tag;
    }
    
    /**
     * 
     * @api
     * @param   
     * @param    
     * @return
     * @link
     */
    public function getTagById ($id)
    {
        $collection = $this->getCollection();
        if(iscollection($collection[$id])) {
            return $collection[$id];
        }
        return null;
    }

    public function addTag(TagInterface $Tag)
    {
        $id = $Tag->getId();
        $this->addCollection($id, $Tag);
        if(!$Tag->getChild()) {
            return false;
        }
        foreach($Tag->getChild() as $child) {
            $this->addTag($child);
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
        foreach($this->getCollection() as $id => $Tag) {
            call_user_func($callback, $id, $Tag);
        }
    }
}