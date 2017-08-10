<?php
declare(strict_types=1);

namespace Framework\Repository\Repository;

interface SqlBuilderInterface
{
    public function setEntityInfo($namespace, $recordInfo);
}