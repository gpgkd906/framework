<?php

namespace Framework\Service\TemplateService\Parser;

use Framework\Service\TemplateService\Parser\Interfaces\TagInterface;

abstract class AbstractTag implements TagInterface
{
    const isGlobalTag = false;
    const isSingleTag = false;
    const isWrapTag = false;
    
    /**
     *
     * @api
     * @var mixed $increamentor
     * @access public
     * @link
     */
    static private $incrementor = 0;
    
    /**
     *
     * @api
     * @var mixed $name 
     * @access private
     * @link
     */
    private $name = null;

    /**
     *
     * @api
     * @var mixed $attrs 
     * @access private
     * @link
     */
    private $attrs = [];
    
    /**
     *
     * @api
     * @var mixed $content 
     * @access private
     * @link
     */
    private $content = null;

    /**
     *
     * @api
     * @var mixed $child 
     * @access private
     * @link
     */
    private $child = [];

    /**
     *
     * @api
     * @var mixed $replace 
     * @access private
     * @link
     */
    private $replace = null;

    /**
     *
     * @api
     * @var mixed $raw 
     * @access private
     * @link
     */
    private $raw = null;

    /**
     *
     * @api
     * @var mixed $elementId 
     * @access private
     * @link
     */
    private $elementId = null;

    /**
     *
     * @api
     * @var mixed $id 
     * @access private
     * @link
     */
    private $id = null;
    
    public function __construct()
    {
        self::$incrementor++;
        $this->setElementId("element_" . self::$incrementor);
    }

    /**
     * 
     * @api
     * @param mixed $name
     * @return mixed $name
     * @link
     */
    public function setName ($name)
    {
        return $this->name = $name;
    }

    /**
     * 
     * @api
     * @return mixed $name
     * @link
     */
    public function getName ()
    {
        return $this->name;
    }

    /**
     * 
     * @api
     * @param mixed $attrs
     * @return mixed $attrs
     * @link
     */
    public function setAttrs ($attrs)
    {
        if(isset($attrs["replace"])) {
            $this->setReplace($attrs["replace"]);
            unset($attrs["replace"]);
        }
        if(isset($attrs["id"])) {
            $this->setId($attrs["id"]);
            unset($attrs["id"]);
        }
        return $this->attrs = $attrs;
    }

    /**
     * 
     * @api
     * @return mixed $attrs
     * @link
     */
    public function getAttrs ()
    {
        return $this->attrs;
    }

    /**
     * 
     * @api
     * @param mixed $content
     * @return mixed $content
     * @link
     */
    public function setContent ($content)
    {
        return $this->content = $content;
    }

    /**
     * 
     * @api
     * @return mixed $content
     * @link
     */
    public function getContent ()
    {
        return $this->content;
    }

    /**
     * 
     * @api
     * @param mixed $child
     * @return mixed $child
     * @link
     */
    public function setChild ($child)
    {
        return $this->child = $child;
    }

    /**
     * 
     * @api
     * @return mixed $child
     * @link
     */
    public function getChild ()
    {
        return $this->child;
    }

    /**
     * 
     * @api
     * @param mixed $replace
     * @return mixed $replace
     * @link
     */
    public function setReplace ($replace)
    {
        return $this->replace = $replace;
    }

    /**
     * 
     * @api
     * @return mixed $replace
     * @link
     */
    public function getReplace ()
    {
        return $this->replace;
    }

    /**
     * 
     * @api
     * @param mixed $raw
     * @return mixed $raw
     * @link
     */
    public function setRaw ($raw)
    {
        return $this->raw = $raw;
    }

    /**
     * 
     * @api
     * @return mixed $raw
     * @link
     */
    public function getRaw ()
    {
        return $this->raw;
    }

    /**
     * 
     * @api
     * @param mixed $elementId
     * @return mixed $elementId
     * @link
     */
    public function setElementId ($elementId)
    {
        return $this->elementId = $elementId;
    }

    /**
     * 
     * @api
     * @return mixed $elementId
     * @link
     */
    public function getElementId ()
    {
        return $this->elementId;
    }

    /**
     * 
     * @api
     * @param mixed $id
     * @return mixed $id
     * @link
     */
    public function setId ($id)
    {
        return $this->id = $id;
    }

    /**
     * 
     * @api
     * @return mixed $id
     * @link
     */
    public function getId ()
    {
        if($this->id === null) {
            $this->id = $this->getElementId();
        }
        return $this->id;
    }

    abstract public function onParse($Parser);
}
