<?php
/**
 * PHP version 7
 * File LayoutInterface.php
 * 
 * @category Module
 * @package  Framework\ViewModel
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
declare(strict_types=1);

namespace Framework\ViewModel;

/**
 * Interface LayoutInterface
 * 
 * @category Interface
 * @package  Framework\ViewModel
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
interface LayoutInterface
{
    /**
     * Method registerStyle
     *
     * @param string  $style    stylesheet
     * @param integer $priority Priority
     * 
     * @return mixed
     */
    public function registerStyle($style, $priority);

    /**
     * Method registerScript
     *
     * @param string  $script   JavaScript
     * @param integer $priority Priority
     * 
     * @return mixed
     */
    public function registerScript($script, $priority);

    /**
     * Method getStyle
     *
     * @return array styleSheet
     */
    public function getStyle();

    /**
     * Method getScript
     *
     * @return array JavaScript
     */
    public function getScript();

    /**
     * Method setAsset
     *
     * @param string $asset Asset
     * 
     * @return mixed
     */
    public function setAsset($asset);

    /**
     * Method getAsset
     *
     * @return string $asset
     */
    public function getAsset();
}
