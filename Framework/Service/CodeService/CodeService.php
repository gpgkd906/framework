<?php

namespace Framework\Service\CodeService;

use Framework\Service\AbstractService;
use Framework\Config\ConfigModel;

use PhpParser\Lexer;
use PhpParser\Error;
use PhpParser\Parser;
use PhpParser\NodeTraverser;
use PhpParser\PrettyPrinter;

class CodeService extends AbstractService
{
    public function __construct()
    {
    }

    public function demo()
    {        
        $this->ls(ROOT_DIR . 'Framework/Controller/Controller', function($fileName, $filePath) {
            $parser = new Parser(new Lexer);
            $traverser = new NodeTraverser;
            $traverser->addVisitor(new NodeVisitor);
            $stmts = $parser->parse(file_get_contents($filePath));
            $stmts = $traverser->traverse($stmts);            
            /* var_dump( */
            /*     get_class_methods($stmts[0]) */
            /*     , $stmts[0] */
            /* ); */
            die;
        });
    }

    public function scan($dir, $exclude = [], $fullPathFlag = true, $basePath = null) {
        if($basePath === null) {
            $basePath = $dir;
        }
        $exclude = (array) $exclude;
        while($dir[strlen($dir) - 1] === '/') {
            $dir = substr($dir, 0, -1);
        }
        if(in_array($dir, $exclude)) {
            return false;
        }

        if(is_dir($dir)) {
            $handler = opendir($dir);
            $result = [];
            while($file = readdir($handler)) {
                if($file === '.'
                || $file === '..'
                || $file[0] === '.'
                || $file[0] === '#'
                || preg_match('/~$/', $file)) {
                    continue;
                }
                $file = str_replace('//', '/', $dir . '/' . $file);
                if(is_dir($file)) {
                    $subdir = $file;
                    if($fullPathFlag === false) {
                        $file = substr($file, strlen($basePath));;
                    }
                    $result[$file] = $this->scan($subdir, $exclude, $fullPathFlag, $basePath);
                } else {
                    if(in_array($file, $exclude)) {
                        continue;
                    }
                    if($fullPathFlag === false) {
                        $file = substr($file, strlen($basePath));;
                    }
                    $result[] = $file;
                }
            }
            return $result;
        }
    }
}