<?php
/**
 * PHP version 7
 * File ValidatorManagerAwareTrait.php
 * 
 * @category Module
 * @package  Std\ValidatorManager
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
declare(strict_types=1);

namespace Std\ValidatorManager;

use Framework\ObjectManager\ObjectManager;

/**
 * Trait ValidatorManagerAwareTrait
 * 
 * @category Trait
 * @package  Std\ValidatorManager
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
trait ValidatorManagerAwareTrait
{
    private static $_ValidatorManager;

    /**
     * Method setValidatorManager
     *
     * @param ValidatorManagerInterface $ValidatorManager ValidatorManager
     * 
     * @return mixed
     */
    public function setValidatorManager(ValidatorManagerInterface $ValidatorManager)
    {
        self::$_ValidatorManager = $ValidatorManager;
    }

    /**
     * Method getValidatorManager
     *
     * @return ValidatorManagerInterface $ValidatorManager
     */
    public function getValidatorManager()
    {
        if (self::$_ValidatorManager === null) {
            self::$_ValidatorManager = ObjectManager::getSingleton()->get(ValidatorManagerInterface::class, ValidatorManager::class);
        }
        return self::$_ValidatorManager;
    }
}
