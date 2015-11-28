<?php

namespace Framework\Service\CodeService\Code\Wrapper;

class NamespaceWrapper extends AbstractWrapper
{
    public function setName($newNamespace)
    {
        $parts = explode('\\', $newNamespace);
        $this->getNamespace()->name->parts = $parts;
    }

    public function appendUse($use, $as = null)
    {
        $factory = $this->getFactory();
        $use = $factory->use($use);
        if($as !== null) {
            $use->as($as);
        }
        array_unshift($this->getNamespace()->stmts, $use->getNode());
    }

    public function addStmt($stmt)
    {

    }
}
