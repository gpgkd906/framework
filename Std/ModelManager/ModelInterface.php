<?php

namespace Std\ModelManager;

interface ModelInterface
{
    public function toArray();

    public function fromArray($data);
}
