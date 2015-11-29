<?php

namespace Framework\Service\CodeService\Code\Wrapper;

use PhpParser\Node;
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
        $return .= ';';        
        $ast = Code\Analytic::analyticCode('<?php return ' . $return);
        $returnNode = $ast->getStmts()[0];
        $node = $this->getNode();
        foreach($node->stmts as $key => $stmt) {
            if($stmt->getType() === $returnNode->getType()) {
                $node->stmts[$key] = $returnNode;
            }
        }
    }

    public function appendProcess($process)
    {
        $process .= ';';
        $ast = Code\Analytic::analyticCode('<?php ' . $process);
        $processNode = $ast->getStmts()[0];        
        $node = $this->getNode();
        $count = count($node->stmts);
        $node->stmts = array_merge(
            array_slice($node->stmts, 0, -1),
            [$processNode],
            array_slice($node->stmts, -1)
        );
    }

    public function appendParam($param)
    {
        $ast = Code\Analytic::analyticCode('<?php function(' . $param . '){};');
        $paramNode = $ast->getStmts()[0]->params[0];
        $this->getNode()->params[] = $paramNode;
    }

    public function getParam($param)
    {
        $param = substr($param, 1);
        $node = $this->getNode();
        foreach($node->params as $paramNode) {
            if($paramNode->name === $param) {
                return new ParamWrapper($paramNode);
            }
        }
    }
}
