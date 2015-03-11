<?php
namespace Framework\Core\ViewModel;

use Framework\Core\Interfaces\ViewModelInterface;

abstract class AbstractViewModel implements ViewModelInterface
{
    private $template = null;
    private $data = null;
    
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
   
    public function renderAsHtml()
    {
        
    }
    
    public function renderAsJson()
    {
        
    }
    
    public function renderAsXml()
    {
        
    }
}