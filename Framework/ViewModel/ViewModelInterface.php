<?php
/**
 * PHP version 7
 * File ViewModelInterface.php
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
 * Interface ViewModelInterface
 * 
 * @category Interface
 * @package  Framework\ViewModel
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
interface ViewModelInterface
{
    /**
     * Method setTemplate
     *
     * @param string $template Template
     * 
     * @return mixed
     */
    public function setTemplate($template);

    /**
     * Method getTemplate
     *
     * @return string $template
     */
    public function getTemplate();

    /**
     * Method setData
     *
     * @param mixed $data Data
     * 
     * @return mixed
     */
    public function setData($data);

    /**
     * Method getId
     *
     * @return string ViewId
     */
    public function getId();

    /**
     * Method getData
     *
     * @return mixed
     */
    public function getData();

    /**
     * Method render
     *
     * @return string responseContent
     */
    public function render();
}
