<?php

namespace Framework\Service\CacheService;

trait CacheServiceAwareTrait
{
    private $CacheService;

    public function setCacheService(CacheService $CacheService)
    {
        $this->CacheService = $CacheService;
    }

    public function getCacheService()
    {
        return $this->CacheService;
    }
}
