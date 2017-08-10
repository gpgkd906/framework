<?php
declare(strict_types=1);

namespace Framework\ObjectManager;

interface FactoryInterface
{
    public function create($ObjectManager);
}
