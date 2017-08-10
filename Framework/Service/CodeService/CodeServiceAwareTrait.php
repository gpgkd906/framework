<?php
declare(strict_types=1);

namespace Framework\Service\CodeService;

trait CodeServiceAwareTrait
{
    private static $CodeService;

    public function setCodeService(CodeService $CodeService)
    {
        self::$CodeService = $CodeService;
    }

    public function getCodeService()
    {
        return self::$CodeService;
    }
}
