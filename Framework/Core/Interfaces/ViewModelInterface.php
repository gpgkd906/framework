<?php

namespace Framework\Core\Interfaces;

interface ViewModelInterface 
{    
    public function setTemplate($template);

    public function getTemplate();

    public function setData($data);

    public function getData();
   
    public function asHtml();
    
    public function asJson();
    
    public function asXml();

    public function render();
    
    public function renderAsHtml();
    
    public function renderAsJson();
    
    public function renderAsXml();
}
