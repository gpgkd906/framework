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
            die;
        });
    }

    public function scan($dir, $exclude = [], $basePath = null, $maxFileSize = 40000) {
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
                    if(!in_array($subdir, $exclude)) {
                        //フォルダーを追加した後に、フォルダーの中身をスキャンする
                        $fullPath = $file;
                        $file = substr($file, strlen($basePath));;
                        $folder = [
                            'dir' => $dir,
                            'file' => $file,
                            'fullPath' => $fullPath,
                            'fileSize' => -1,
                            'nameHash' => md5($file),
                            'fileHash' => null,
                        ];
                        $result[] = $folder;
                        $result = array_merge($result, $this->scan($subdir, $exclude, $basePath));
                    }
                } else {
                    if(in_array($file, $exclude)) {
                        continue;
                    }
                    $filesize = filesize($file);
                    if($filesize > $maxFileSize) {
                        //大きなファイルを処理しない、誰がそんな大きなソースを書くんだ
                        continue;
                    }
                    $fullPath = $file;
                    $file = substr($file, strlen($basePath));;
                    $result[] = [
                        'dir' => $dir,
                        'file' => $file,
                        'fullPath' => $fullPath,
                        'fileSize' => $filesize,
                        'nameHash' => md5($file),
                        'fileHash' => md5_file($fullPath),
                    ];
                }
            }
            return $result;
        }
    }

    public function analysis($file)
    {

    }
}