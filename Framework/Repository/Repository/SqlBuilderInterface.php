<?php

namespace Framework\Repository\Repository;

interface SqlBuilderInterface
{
    public function setEntityInfo($namespace, $recordInfo);
}