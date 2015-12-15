<?php

namespace Framework\Service\CodeService\Code\Wrapper;

class NamespaceWrapper extends AbstractWrapper
{
    public function setName($newNamespace)
    {
        $parts = explode('\\', $newNamespace);
        $this->getNode()->name->parts = $parts;
    }

    public function appendUse($use, $as = null)
    {
        $factory = $this->getFactory();
        $use = $factory->use($use);
        if($as !== null) {
            $use->as($as);
        }
        array_unshift($this->getNode()->stmts, $use->getNode());
    }

    public function addStmt($stmt)
    {

    }

    public function getNameSpace()
    {
        return $this->getNode()->name->toString();
    }
}
