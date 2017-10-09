<?php
/**
 * PHP version 7
 * File SingletonTrait.php
 *
 * @category Trait
 * @package  Framework\ObjectManager
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
declare(strict_types=1);
namespace Framework\ObjectManager;

/**
 * Trait SingletonTrait
 *
 * @category Trait
 * @package  Framework\ObjectManager
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
trait SingletonTrait
{

    private static $_instance = null;

    /**
     * Method getSingleton
     *
     * @return SingletonInterface this
     */
    public static function getSingleton()
    {
        $className = static::class;
        if (!isset(self::$_instance)) {
            self::$_instance = new $className();
        }
        return self::$_instance;
    }
}
