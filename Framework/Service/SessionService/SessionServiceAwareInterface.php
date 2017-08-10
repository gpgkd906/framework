<?php
declare(strict_types=1);

namespace Framework\Service\SessionService;

interface SessionServiceAwareInterface
{
    public function setSessionService(SessionService $SessionService);
    public function getSessionService();
}
