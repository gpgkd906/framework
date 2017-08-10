<?php
declare(strict_types=1);

namespace Framework\Router;

interface RouterAwareInterface
{
    public function setRouter(RouterInterface $Router);
    public function getRouter();
}
