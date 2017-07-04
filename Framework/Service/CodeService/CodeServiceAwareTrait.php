<?php

namespace Framework\Service\CodeService;

trait CodeServiceAwareTrait
{
    private $CodeService;

    public function setCodeService(CodeService $CodeService)
    {
        $this->CodeService = $CodeService;
    }

    public function getCodeService()
    {
        return $this->CodeService;
    }
}
