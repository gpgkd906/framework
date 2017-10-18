<?php
/**
 * PHP version 7
 * File ViewModelManagerAwareTrait.php
 *
 * @category ViewModel
 * @package  Framework\ViewModel
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
declare(strict_types=1);

namespace Framework\ViewModel;

use Framework\ObjectManager\ObjectManager;

/**
 * Trait ViewModelManagerAwareTrait
 *
 * @category Trait
 * @package  Framework\ViewModelManager
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
trait ViewModelManagerAwareTrait
{
    private static $_ViewModelManager;

    /**
     * Method setViewModelManager
     *
     * @param ViewModelManagerInterface $ViewModelManager ViewModelManager
     * @return this
     */
    public function setViewModelManager(ViewModelManagerInterface $ViewModelManager)
    {
        self::$_ViewModelManager = $ViewModelManager;
        return $this;
    }

    /**
     * Method getViewModelManager
     *
     * @return ViewModelManagerInterface $ViewModelManager
     */
    public function getViewModelManager()
    {
        return self::$_ViewModelManager;
    }
}
