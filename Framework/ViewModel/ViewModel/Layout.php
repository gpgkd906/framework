<?php

namespace Framework\ViewModel\ViewModel;

use \Exception;

class Layout implements LayoutInterface
{
    const ERROR_REGISTER_STYLE_FOR_DIFFERENT_PRIORITY = 'register_style_for_different_priority: %s';
    const ERROR_REGISTER_SCRIPT_FOR_DIFFERENT_PRIORITY = 'register_script_for_different_priority: %s';
    
    use \Framework\Application\SingletonTrait;
    
    protected $styles = [];
    
    protected $scripts = [];
    
    /**
     *
     * @api
     * @var mixed $asset 
     * @access private
     * @link
     */
    protected $asset = null;

    private function __construct ()
    {
        $styles = $this->styles;
        $scripts = $this->scripts;
        /* $this->styles = $this->scripts = []; */
        /* foreach($styles as $key => $style) { */
        /*     $this->styles[$style] = $key;             */
        /* } */
        /* foreach($scripts as $key => $script) { */
        /*     $this->scripts[$script] = $key; */
        /* } */
    }

    public function registerStyle($style, $priority = null)
    {
        if($priority === null) {
            $priority = 99;
        }
        if(in_array($style, $this->styles) && array_search($style, $this->styles) == $priority) {
            throw new Exception(sprintf(self::ERROR_REGISTER_STYLE_FOR_DIFFERENT_PRIORITY, $style));
        }
        $this->styles = array_merge(array_slice($this->styles, 0, $priority), [$style], array_slice($this->styles, $priority));
    }
    
    public function registerScript($script, $priority)
    {
        if($priority === null) {
            $priority = 99;
        }
        if(in_array($script, $this->scripts) && array_search($script, $this->scripts) == $priority) {
            throw new Exception(sprintf(self::ERROR_REGISTER_SCRIPT_FOR_DIFFERENT_PRIORITY, $script));
        }
        $this->scripts = array_merge(array_slice($this->scripts, 0, $priority), [$script], array_slice($this->scripts, $priority));
    }
    
    public function getStyle()
    {
        $asset = $this->getAsset();
        $basePath = ViewModelManager::getBasePath();
        $styles = [];
        foreach($this->styles as $style) {
            $styles[] = '//' . $basePath . $asset . $style;
        }
        return $styles;
    }
    
    public function getScript()
    {
        $asset = $this->getAsset();
        $basePath = ViewModelManager::getBasePath();
        $scripts = [];
        foreach($this->scripts as $script) {
            $scripts[] = '//' . $basePath . $asset . $script;
        }
        return $scripts;
    }
    
    public function useStyle($style)
    {

    }
    
    public function useScript($script)
    {

    }

    /**
     * 
     * @api
     * @param mixed $asset
     * @return mixed $asset
     * @link
     */
    public function setAsset ($asset)
    {
        return $this->asset = $asset;
    }

    /**
     * 
     * @api
     * @return mixed $asset
     * @link
     */
    public function getAsset ()
    {
        return $this->asset;
    }
}
