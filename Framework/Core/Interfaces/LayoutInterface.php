<?php

namespace Framework\Core\Interfaces;

interface LayoutInterface
{
    public function registerStyle($style, $priority);
    
    public function registerScript($script, $priority);
    
    public function getStyle();
    
    public function getScript();
    
    public function useStyle();
    
    public function useScript();    
}