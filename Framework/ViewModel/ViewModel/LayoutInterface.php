<?php

namespace Framework\ViewModel\ViewModel;

interface LayoutInterface
{
    public function registerStyle($style, $priority);
    
    public function registerScript($script, $priority);
    
    public function getStyle();
    
    public function getScript();
    
    public function useStyle($style);
    
    public function useScript($script);

    public function setAsset($asset);

    public function getAsset();
}