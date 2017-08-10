<?php
declare(strict_types=1);

namespace Framework\Service\CacheService;

interface CacheServiceAwareInterface
{
    public function setCacheService(CacheService $CacheService);
    public function getCacheService();
}
