<?php
/**
 * PHP version 7
 * File TranslatorManagerAwareTrait.php
 * 
 * @category Module
 * @package  Framework\TranslatorManager
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
declare(strict_types=1);

namespace Framework\TranslatorManager;

use Framework\ObjectManager\ObjectManager;

/**
 * Trait TranslatorManagerAwareTrait
 * 
 * @category Trait
 * @package  Framework\TranslatorManager
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
trait TranslatorManagerAwareTrait
{
    private static $_TranslatorManager;

    /**
     * Method setTranslatorManager
     *
     * @param TranslatorManagerInterface $TranslatorManager Object
     * 
     * @return mixed
     */
    public function setTranslatorManager(TranslatorManagerInterface $TranslatorManager)
    {
        self::$_TranslatorManager = $TranslatorManager;
    }

    /**
     * Method getTranslatorManager
     *
     * @return TranslatorManagerInterface $TranslatorManager
     */
    public function getTranslatorManager()
    {
        if (self::$_TranslatorManager === null) {
            self::$_TranslatorManager = ObjectManager::getSingleton()->get(TranslatorManagerInterface::class, TranslatorManager::class);
        }
        return self::$_TranslatorManager;
    }
}
