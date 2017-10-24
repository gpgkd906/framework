<?php
declare(strict_types=1);

namespace Std\Repository\Repository;

interface SqlBuilderInterface
{
    public function setEntityInfo($namespace, $recordInfo);
}