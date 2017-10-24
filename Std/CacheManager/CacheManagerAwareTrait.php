<?php
/**
 * PHP version 7
 * File CacheManagerAwareTrait.php
 *
 * @category Service
 * @package  Std
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
declare(strict_types=1);

namespace Std\CacheManager;

/**
 * Trait CacheManagerAwareTrait
 *
 * @category Trait
 * @package  Std
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
trait CacheManagerAwareTrait
{
    private static $_CacheManager;

    /**
     * Method setCacheManager
     *
     * @param CacheManager $CacheManager CacheManager
     *
     * @return mixed
     */
    public function setCacheManager(CacheManager $CacheManager)
    {
        self::$_CacheManager = $CacheManager;
    }

    /**
     * Method getCacheManager
     *
     * @return CacheManager $CacheManager
     */
    public function getCacheManager()
    {
        return self::$_CacheManager;
    }
}
