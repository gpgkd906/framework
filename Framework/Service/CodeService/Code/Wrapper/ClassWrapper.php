<?php

namespace Framework\Service\CodeService\Code\Wrapper;

use PhpParser\Builder\Class_;
use PhpParser\Node\Stmt\TraitUse;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Name\Relative;
use PhpParser\Node\Stmt\ClassConst;
use PhpParser\Node\Const_;
use PhpParser\Node\Scalar;
use PhpParser\Node\Expr;

class ClassWrapper extends AbstractWrapper
{
    private $group = [
        'Stmt_TraitUse'    => [],
        'Stmt_ClassConst'  => [],
        'Stmt_Property'    => [],
        'Stmt_ClassMethod' => [],        
    ];

    public function getName()
    {
        return $this->getNode()->name;
    }

    public function setName($newClass)
    {
        $this->getNode()->name = $newClass;
    }

    public function extend($extend)
    {
        if($this->getNode()->extends === null) {
            $this->getNode()->extends = new Name($extend);
        } else {
            $this->getNode()->extends->parts = explode('\\', $extend);
        }
    }

    public function appendImplement($interface)
    {
        $this->getNode()->implements[] = new Name($interface);
    }

    public function appendTrait($trait)
    {
        $name = new FullyQualified($trait);
        $trait = new TraitUse([$name]);
        $this->addStmt($trait);
    }

    public function getTrait($trait)
    {
        $test = new FullyQualified($trait);
        $node = $this->findNode('Stmt_TraitUse', function($stmt) use ($test) {
            return $stmt->traits[0]->parts === $test->parts;            
        });
        return new TraitUseWrapper($node);
    }

    public function appendConst($const, $value = null)
    {
        switch(true) {
        case is_string($value):
            $value = new Scalar\String_($value);
            break;
        case is_integer($value):
            $value = new Scalar\LNumber($value);
            break;
        case is_float($value):
            $value = new Scalar\DNumber($value);
            break;
        case is_bool($value):
            if($value) {
                $value = new Expr\ConstFetch(new Name('true'));
            } else {
                $value = new Expr\ConstFetch(new Name('false'));
            }
            break;
        default:
            $value = new Expr\ConstFetch(new Name('null'));
            break;
        }
        $const = new Const_($const, $value);
        $classConst = new ClassConst([$const]);
        $this->addStmt($classConst);
    }

    public function getConst($const)
    {
        $node = $this->findNode('Stmt_ClassConst', function($stmt) use ($const) {
            return $stmt->consts[0]->name === $const;
        });
        return new ConstWrapper($node);
    }
    
    public function appendProperty($property, $value = null, $access = 'private')
    {
        $factory = $this->getFactory();
        $property = $factory->property($property);
        $accessControl = 'make' . ucfirst($access);
        call_user_func([$property, $accessControl]);
        $property->setDefault($value);
        $this->addStmt($property->getNode());
    }
    
    public function getProperty($property)
    {
        $node = $this->findNode('Stmt_Property', function($stmt) use($property) {
            return $stmt->props[0]->name === $property;
        });
        return new PropertyWrapper($node);
    }

    public function appendMethod($name, $access = 'public')
    {
        $factory = $this->getFactory();
        $method = $factory->method($name);
        $accessControl = 'make' . ucfirst($access);
        call_user_func([$method, $accessControl]);
        $this->addStmt($method->getNode());
    }

    public function getMethod($method)
    {
        $node = $this->findNode('Stmt_ClassMethod', function($stmt) use($method) {
            return $stmt->name === $method;
        });
        return new FuncWrapper($node);
    }

    private function findNode($nodeType, $finder)
    {
        foreach($this->getNode()->stmts as $stmt) {
            if($stmt->getType() !== $nodeType) {
                continue;
            }
            if(call_user_func($finder, $stmt)) {
                return $stmt;
            }
        }
    }

    public function addStmt($stmt)
    {
        $sort = [
            'Stmt_TraitUse'    => 0,
            'Stmt_ClassConst'  => 1,
            'Stmt_Property'    => 2,
            'Stmt_ClassMethod' => 3,
        ];
        if(!isset($sort[$stmt->getType()])) {
            throw new Exception('Cannot add stmt not belong classNode');
        }
        $this->getNode()->stmts[] = $stmt;
        usort($this->getNode()->stmts, function($a, $b) use ($sort){
            Return $sort[$a->getType()] >= $sort[$b->getType()];
        });
    }
}