<?php
namespace Framework\Service\CodeService\Code;

class Modifier
{
    private $oldNode = null;
    private $newNode = null;
    static private $instance = null;

    static public function getSingleton()
    {
        if(self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }
    
    static public function modify($codeEntity, $oldNode, $newNode)
    {
        $instance = self::getSingleton();
        $instance->setOldNode($oldNode);
        $instance->setNewNode($newNode);
        $stmts = $instance->reduce($codeEntity->getStmts());
        $codeEntity->setStmts($stmts);
    }

    protected function setOldNode ($oldNode)
    {
        return $this->oldNode = $oldNode;
    }

    protected function getOldNode ()
    {
        return $this->oldNode;
    }

    protected function setNewNode ($newNode)
    {
        return $this->newNode = $newNode;
    }

    protected function getNewNode ()
    {
        return $this->newNode;
    }

    protected function reduce($stmts)
    {
        foreach($stmts as $key => $stmt) {
            if($stmt === $this->getOldNode()) {
                $stmts[$key] = $this->getNewNode();
                break;
            }
            if(property_exists($stmt, 'stmts')) {
                $stmt->stmts = $this->reduce($stmt->stmts);
            }
        }
        return $stmts;
    }
}
