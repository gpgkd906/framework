<?php
namespace Framework\Core\ViewModel;

use Framework\Core\Interfaces\ViewModelInterface;

abstract class AbstractViewModel implements ViewModelInterface
{
    const renderAsHTML = "html";
    const renderAsJSON = "json";
    const renderAsXML = "xml";

    //error
    const ERROR_INVALID_RENDER_TYPE = "error: invalid render-type";

    
    private $template = null;
    private $data = null;
    private $renderType = "html";
    private $layout = null;
    private $items = [];
    private $childs = [];
    
    public function setRenderType($renderType)
    {
        $this->renderType = $renderType;
    }

    public function getRenderType()
    {
        return $this->renderType;
    }

    public function setItems($items)
    {
        $this->items = $items;
    }

    public function getItems()
    {
        return $this->items;
    }
    
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    public function getTemplate()
    {
        return $this->template;
    }
    
    public function setData($data)
    {
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }
    
    public function addChild(ViewModelInterface $ViewModel)
    {
        $ViewModel->setRenderType($this->getRenderType());
        $this->childs[] = $ViewModel;
    }
    
    public function getChilds()
    {
        return $this->childs;
    }
    
    public function render($renderType = null)
    {
        if($renderType === null) {
            $renderType = $this->$renderType;
        }
        switch($renderType) {
        case static::renderAsHTML:
            return $this->renderAsHTML();
            break;
        case static::renderAsJSON:
            return $this->renderAsJSON();
            break;
        case static::renderAsXML:
            return $this->renderAsXML();
            break;
        default:
            throw new Exception(self::ERROR_INVALID_RENDER_TYPE);
            break;
        }
    }

    public function renderAsHtml()
    {
        
        foreach($this->childs as $child) {
            $child->renderAsHtml();
        }
    }
    
    public function renderAsJson()
    {
        
    }
    
    public function renderAsXml()
    {
        
    }

    public function getLayout()
    {
        if($this->layout !== null) {
            $layout = $this->layout;
            return $layoutView = new $layout;
        }
    }

    public function __toString()
    {
        return $this->render();
    }
}