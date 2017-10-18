<?php
/**
 * PHP version 7
 * File ViewModelManagerAwareInterface.php
 *
 * @category ViewModel
 * @package  Framework\ViewModel
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
declare(strict_types=1);

namespace Framework\ViewModel;

/**
 * Interface ViewModelManagerAwareInterface
 *
 * @category Interface
 * @package  Framework\ViewModelManager
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
interface ViewModelManagerAwareInterface
{
    /**
     * Method setViewModelManager
     *
     * @param ViewModelManagerInterface $ViewModelManager ViewModelManager
     * @return mixed
     */
    public function setViewModelManager(ViewModelManagerInterface $ViewModelManager);

    /**
     * Method getViewModelManager
     *
     * @return ViewModelManagerInterface $ViewModelManager
     */
    public function getViewModelManager();
}
