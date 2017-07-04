<?php

namespace Framework\Service\CodeService;

use Framework\Service\AbstractService;
use CodeService\Code\Analytic;
use CodeService\Code\Wrapper\AstWrapper;
use Framework\Config\ConfigModel;

class CodeService extends AbstractService
{
    public function scan($dir, $exclude = [], $basePath = null, $maxFileSize = 40000, $depth = 0) {
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
                if($file[0] === '.'
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
                            'dirHash' => md5($dir),
                            'file' => $file,
                            'fullPath' => $fullPath,
                            'fileSize' => -1,
                            'nameHash' => md5($file),
                            'fileHash' => md5($fullPath),
                            'depth'    => $depth,
                        ];
                        $result[] = $folder;
                        $result = array_merge($result, $this->scan($subdir, $exclude, $basePath, $maxFileSize, $depth + 1));
                    }
                } else {
                    if(in_array($file, $exclude)) {
                        continue;
                    }
                    $filesize = filesize($file);
                    if($filesize > $maxFileSize) {
                        continue;
                    }
                    $fullPath = $file;
                    $file = substr($file, strlen($basePath));
                    $temp = [
                        'dir' => $dir,
                        'dirHash' => md5($dir),
                        'file' => $file,
                        'fullPath' => $fullPath,
                        'fileSize' => $filesize,
                        'nameHash' => md5($file),
                        'fileHash' => md5_file($fullPath),
                        'depth'    => $depth,
                    ];
                    $result[] = $temp;
                }
            }
            return $result;
        }
    }

    public function scanFile($filePath)
    {

    }

    public function analysis($file, $version = null)
    {
        return Analytic::analytic($file, $version);
    }

    public function analysisCode($code, $version = null)
    {
        return Analytic::analyticCode($code, $version);
    }

    public function createCode($namespace = null, $class = null)
    {
        $codeBase = [
            '<?php', PHP_EOL, PHP_EOL
        ];
        if ($namespace) {
            $codeBase[] = 'namespace ';
            $codeBase[] = $namespace . ';';
            $codeBase[] = PHP_EOL;
        }
        if ($class) {
            $codeBase[] = 'class ';
            $codeBase[] = $class . ' {}';
            $codeBase[] = PHP_EOL;
        }
        // var_dump(join('', $codeBase));die;
        return Analytic::analyticCode(join('', $codeBase));
    }

    public function test()
    {
        $file = "/Users/gpgkd906/dev/framework/Framework/Controller/Admin/CustomerController.php";
        $ast = Analytic::analytic($file);
        $ast->getClass()->extend('AbstractService');
        $ast->getClass()->appendImplement('ObjectManagerAwareInterface');
        $ast->getClass()->appendConst('Test', false);
        $ast->getClass()->appendProperty('objectManager');
        $ast->getClass()->appendMethod('onRender', 'public');
        $ast->getClass()->getMethod('index')->setReturn("ViewModelManager::getViewModel([ 'viewModel' => PageViewModel::class ]);");
        $ast->getClass()->getMethod('index')->appendProcess('$Model = new Test;');
        $ast->getClass()->getMethod('index')->appendParam('$dir = "/test/"');
        $ast->getClass()->getMethod('index')->getParam('$dir');
        $ast->getClass()->getTrait('Framework\Event\Event\EventTargetTrait');
        echo '<pre><code>';
        print($ast->toHtml());
        echo '</code></pre>';
    }
}
