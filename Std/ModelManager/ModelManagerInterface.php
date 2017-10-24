<?php
declare(strict_types=1);

namespace Std\ModelManager;

interface ModelManagerInterface
{
    public function getModel($ModelClass, $record);

    public function getIterator($ModelClass, $data);
}
