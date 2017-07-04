<?php

namespace Framework\Service\SessionService;

interface SessionServiceAwareInterface
{
    public function setSessionService(SessionService $SessionService);
    public function getSessionService();
}
