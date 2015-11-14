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

    public function ls($dir, $func, $pass = array()) {
        $_check = preg_replace("/\/$/", "", $dir);
        if(in_array($_check, $pass)) {
            return false;
        }
        if(is_dir($dir)) {
            $handler = opendir($dir);
            while($file = readdir($handler)) {
                if($file === "." || $file === ".." || preg_match("/~$/", $file)) {
                    continue;
                }
                $_file = str_replace("//", "/", $dir . "/" . $file);
                if(is_dir($_file)) {
                    call_user_func("self::ls", $_file, $func, $pass);
                } else {
                    if(in_array($_file, $pass)) {
                        //skip the file                                                                                                                                                                     
                        continue;
                    }
                    call_user_func($func, $file, $_file);
                }
            }
        }
    }
}