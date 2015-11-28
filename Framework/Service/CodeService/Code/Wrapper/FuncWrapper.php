<?php

namespace Framework\Service\CodeService\Code\Wrapper;

use PhpParser\Node\Name;
use PhpParser\Node\Const_;
use PhpParser\Node\Scalar;
use PhpParser\Node\Expr;
use PhpParser\Node\Stmt;
use Framework\Service\CodeService\Code;

class FuncWrapper extends AbstractWrapper
{
    
    public function setReturn($return)
    {        
        $ast = Code\Analytic::analyticCode('<?php return ' . $return);
        $returnNode = $ast->getStmts()[0];
        $node = $this->getNode();
        foreach($node->stmts as $key => $stmt) {
            if($stmt->getType() === $returnNode->getType()) {
                $node->stmts[$key] = $returnNode;
            }
        }
    }

    public function appendParam()
    {
        
    }

    public function getParam()
    {
        
    }
}
