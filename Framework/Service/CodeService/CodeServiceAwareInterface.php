<?php
declare(strict_types=1);

namespace Framework\Service\CodeService;

interface CodeServiceAwareInterface
{
    public function setCodeService(CodeService $CodeService);
    public function getCodeService();
}
