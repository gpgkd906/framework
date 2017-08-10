<?php
declare(strict_types=1);

namespace Framework\ObjectManager;

interface SingletonInterface
{
    public static function getSingleton();
}
