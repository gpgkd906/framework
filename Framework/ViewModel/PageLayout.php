<?php
/**
 * PHP version 7
 * File PageLayout.php
 * 
 * @category Module
 * @package  Framework\ViewModel
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
declare(strict_types=1);

namespace Framework\ViewModel;

use Framework\ObjectManager\SingletonInterface;
use Exception;

/**
 * Interface PageLayout
 * 
 * @category Interface
 * @package  Framework\ViewModel
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
class PageLayout extends AbstractViewModel implements 
    LayoutInterface, 
    SingletonInterface
{
    use \Framework\ObjectManager\SingletonTrait;

    const ERROR_REGISTER_STYLE_FOR_DIFFERENT_PRIORITY = 'register_style_for_different_priority: %s';
    const ERROR_REGISTER_SCRIPT_FOR_DIFFERENT_PRIORITY = 'register_script_for_different_priority: %s';

    protected $styles = [];
    protected $scripts = [];
    protected $config = [
        'container' => [
            'Main' => [],
        ]
    ];
    protected $asset = null;

    /**
     * Method registerStyle
     *
     * @param string  $style    stylesheet
     * @param integer $priority Priority
     * 
     * @return this
     */
    public function registerStyle($style, $priority = null)
    {
        if ($priority === null) {
            $priority = 99;
        }
        if (in_array($style, $this->styles)) {
            return false;
        }
        $this->styles = array_merge(array_slice($this->styles, 0, $priority), [$style], array_slice($this->styles, $priority));
        return $this;
    }

    /**
     * Method registerScript
     *
     * @param string  $script   JavaScript
     * @param integer $priority Priority
     * 
     * @return this
     */
    public function registerScript($script, $priority = null)
    {
        if ($priority === null) {
            $priority = 99;
        }
        if (in_array($script, $this->scripts)) {
            return false;
        }
        $this->scripts = array_merge(array_slice($this->scripts, 0, $priority), [$script], array_slice($this->scripts, $priority));
        return $this;
    }

    /**
     * Method getStyle
     *
     * @return array styleSheet
     */
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

    /**
     * Method getScript
     *
     * @return array JavaScript
     */
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
     * Method setAsset
     *
     * @param string $asset Asset
     * 
     * @return this
     */
    public function setAsset($asset)
    {
        $this->asset = $asset;
        return $this;
    }

    /**
     * Method getAsset
     *
     * @return string $asset
     */
    public function getAsset()
    {
        return $this->asset;
    }
}
