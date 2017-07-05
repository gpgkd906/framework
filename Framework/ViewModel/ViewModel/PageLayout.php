<?php

namespace Framework\ViewModel\ViewModel;

use Framework\ObjectManager\SingletonInterface;
use \Exception;

class PageLayout implements LayoutInterface, SingletonInterface
{
    const ERROR_REGISTER_STYLE_FOR_DIFFERENT_PRIORITY = 'register_style_for_different_priority: %s';
    const ERROR_REGISTER_SCRIPT_FOR_DIFFERENT_PRIORITY = 'register_script_for_different_priority: %s';
    
    use \Framework\ObjectManager\SingletonTrait;
    
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

    /**
     *
     * @api
     * @var mixed $pageVar 
     * @access private
     * @link
     */
    private $pageVar = [];

    private function __construct ()
    {
        $styles = $this->styles;
        $scripts = $this->scripts;
    }

    public function registerStyle($style, $priority = null)
    {
        if ($priority === null) {
            $priority = 99;
        }
        if (in_array($style, $this->styles)) {
            return false;
        }
        $this->styles = array_merge(array_slice($this->styles, 0, $priority), [$style], array_slice($this->styles, $priority));
    }
    
    public function registerScript($script, $priority = null)
    {
        if ($priority === null) {
            $priority = 99;
        }
        if (in_array($script, $this->scripts)) {
            return false;
        }
        $this->scripts = array_merge(array_slice($this->scripts, 0, $priority), [$script], array_slice($this->scripts, $priority));
    }
    
    public function getStyle()
    {
        $asset = $this->getAsset();
        $basePath = ViewModelManager::getBasePath();
        $styles = [];
        foreach ($this->styles as $style) {
            $styles[] = '//' . $basePath . $asset . $style;
        }
        return $styles;
    }
    
    public function getScript()
    {
        $asset = $this->getAsset();
        $basePath = ViewModelManager::getBasePath();
        $scripts = [];
        foreach ($this->scripts as $script) {
            $scripts[] = '//' . $basePath . $asset . $script;
        }
        return $scripts;
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

    /**
     * 
     * @api
     * @param array $pageVars
     * @return
     * @link
     */
    public function setPageVars ($pageVars)
    {
        $this->pageVar = $pageVars;
    }

    /**
     * 
     * @api
     * @param mixed $pageVar
     * @return mixed $pageVar
     * @link
     */
    public function setPageVar ($varName, $value)
    {
        return $this->pageVar[$varName] = $value;
    }

    /**
     * 
     * @api
     * @return mixed $pageVar
     * @link
     */
    public function getPageVar ($varName)
    {
        return $this->pageVar[$varName];
    }
}
