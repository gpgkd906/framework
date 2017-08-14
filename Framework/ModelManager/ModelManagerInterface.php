<?php
declare(strict_types=1);

namespace Framework\ModelManager;

interface ModelManagerInterface
{
    public function getModel($ModelClass, $record);

    public function getIterator($ModelClass, $data);
}
