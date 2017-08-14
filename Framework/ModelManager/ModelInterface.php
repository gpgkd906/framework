<?php

namespace Framework\ModelManager;

interface ModelInterface
{
    public function toArray();

    public function fromArray($data);
}
