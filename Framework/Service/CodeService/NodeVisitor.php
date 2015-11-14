<?php
namespace Framework\Service\CodeService;

use PhpParser\NodeVisitorAbstract;
use PhpParser\Node;

class NodeVisitor extends NodeVisitorAbstract
{ 
    public function enterNode(Node $node) {
        if($node instanceof \PhpParser\Node\Stmt\ClassMethod) {
            var_dump(
                //$node
                get_class_methods($node)
                //$node->getLine()
            );

            $factory = new \PhpParser\BuilderFactory;
            var_dump($factory->method('test'));
            die;
        }
    }
}

