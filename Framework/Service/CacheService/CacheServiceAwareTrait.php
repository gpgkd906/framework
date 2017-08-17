<?php
/**
 * PHP version 7
 * File CacheServiceAwareTrait.php
 * 
 * @category Service
 * @package  Framework\Service
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
declare(strict_types=1);

namespace Framework\Service\CacheService;

/**
 * Trait CacheServiceAwareTrait
 * 
 * @category Trait
 * @package  Framework\Service
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
trait CacheServiceAwareTrait
{
    private static $_CacheService;

    /**
     * Method setCacheService
     *
     * @param CacheService $CacheService CacheService
     * 
     * @return mixed
     */
    public function setCacheService(CacheService $CacheService)
    {
        self::$_CacheService = $CacheService;
    }

    /**
     * Method getCacheService
     *
     * @return CacheService $CacheService
     */
    public function getCacheService()
    {
        return self::$_CacheService;
    }
}
