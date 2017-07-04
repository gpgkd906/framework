<?php

namespace Framework\Service\CodeService;

interface CodeServiceAwareInterface
{
    public function setCodeService(CodeService $CodeService);
    public function getCodeService();
}
