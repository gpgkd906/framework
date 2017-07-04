<?php

namespace Framework\ObjectManager;

interface ObjectManagerInterface
{
    public function get($name, $factory = null);
}
