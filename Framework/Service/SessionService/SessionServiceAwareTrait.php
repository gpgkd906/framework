<?php
declare(strict_types=1);

namespace Framework\Service\SessionService;

trait SessionServiceAwareTrait
{
    private static $SessionService;

    public function setSessionService(SessionService $SessionService)
    {
        self::$SessionService = $SessionService;
    }

    public function getSessionService()
    {
        return self::$SessionService;
    }
}
