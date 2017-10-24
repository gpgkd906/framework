<?php
declare(strict_types=1);

namespace Std\ModelManager;

interface ModelManagerAwareInterface
{
    public function setModelManager(ModelManagerInterface $ModelManager);
    public function getModelManager();
}
