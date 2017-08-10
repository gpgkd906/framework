<?php
declare(strict_types=1);

namespace Framework\Service\CacheService;

trait CacheServiceAwareTrait
{
    private static $CacheService;

    public function setCacheService(CacheService $CacheService)
    {
        self::$CacheService = $CacheService;
    }

    public function getCacheService()
    {
        return self::$CacheService;
    }
}
