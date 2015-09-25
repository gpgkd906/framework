<?php

namespace Framework\ViewModel\ViewModel;

trait LayoutTrait
{
    private $styles = [];
    
    private $scripts = [];
    
    public function registerStyle($style, $priority = null)
    {
        if($priority === null) {
            $priority = 99;
        }
        $this->styles[] = [
            'style' => $style,
            'priority' => $priority
        ];
    }
    
    public function registerScript($script, $priority)
    {
        if($priority === null) {
            $priority = 99;
        }
        $this->styles[] = [
            'script' => $script,
            'priority' => $priority
        ];        
    }
    
    public function getStyle()
    {
        return $this->styles;
    }
    
    public function getScript();
    
    public function useStyle();
    
    public function useScript();    
}
