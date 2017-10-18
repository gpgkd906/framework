<?php
/**
 * PHP version 7
 * File ViewModelManagerInterface.php
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
 * Interface ViewModelManagerInterface
 *
 * @category Interface
 * @package  Framework\ViewModel
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
interface ViewModelManagerInterface
{
    /**
     * Method getViewModel
     *
     * @param array $viewModelConfig ViewModelConfig
     *
     * @return ViewModelManagerInterface ViewModel
     */
    public function getViewModel(array $viewModelConfig);

    /**
     * Method setTemplateDir
     *
     * @param string $templateDir templateDir
     *
     * @return mixed
     */
    public function setTemplateDir(string $templateDir);

    /**
     * render ViewModelをレンダリングする
     *
     * @param ViewModelInterface $viewModel
     * @return void
     */
    public function render(ViewModelInterface $viewModel);
}
