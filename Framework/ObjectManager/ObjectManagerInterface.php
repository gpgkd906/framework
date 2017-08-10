<?php
declare(strict_types=1);

namespace Framework\ObjectManager;

interface ObjectManagerInterface
{
    public function get($name, $factory = null);
}
