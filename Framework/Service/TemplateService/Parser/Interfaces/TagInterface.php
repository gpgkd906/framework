<?php
namespace Framework\Service\TemplateService\Parser\Interfaces;

interface TagInterface
{
    /**
     * 
     * @api
     * @param mixed $attrs
     * @return mixed $attrs
     * @link
     */
    public function setAttrs ($attrs);

    /**
     * 
     * @api
     * @return mixed $attrs
     * @link
     */
    public function getAttrs ();

    /**
     * 
     * @api
     * @param mixed $content
     * @return mixed $content
     * @link
     */
    public function setContent ($content);

    /**
     * 
     * @api
     * @return mixed $content
     * @link
     */
    public function getContent ();

    /**
     * 
     * @api
     * @param mixed $child
     * @return mixed $child
     * @link
     */
    public function setChild ($child);

    /**
     * 
     * @api
     * @return mixed $child
     * @link
     */
    public function getChild ();

    /**
     * 
     * @api
     * @param mixed $replace
     * @return mixed $replace
     * @link
     */
    public function setReplace ($replace);

    /**
     * 
     * @api
     * @return mixed $replace
     * @link
     */
    public function getReplace ();
    
    /**
     * 
     * @api
     * @param mixed $raw
     * @return mixed $raw
     * @link
     */
    public function setRaw ($raw);

    /**
     * 
     * @api
     * @return mixed $raw
     * @link
     */
    public function getRaw ();

    /**
     * 
     * @api
     * @param   
     * @param    
     * @return
     * @link
     */
    public function onParse ($Parser);
}
