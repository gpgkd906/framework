<?php

namespace Framework\Service\TemplateService;

use Framework\Core\AbstractService;
use ZipArchive;

class TemplateService extends AbstractService
{
    /**
     *
     * @api
     * @var mixed $parser 
     * @access private
     * @link
     */
    private $parser = null;

    /**
     *
     * @api
     * @var mixed $files 
     * @access private
     * @link
     */
    private $results = [];

    /**
     *
     * @api
     * @var mixed $baseDir 
     * @access private
     * @link
     */
    private $baseDir = null;

    /**
     *
     * @api
     * @var mixed $targetDir 
     * @access private
     * @link
     */
    private $targetDir = null;

    /**
     * 
     * @api
     * @return mixed $parser
     * @link
     */
    public function getParser ()
    {
        if($this->parser === null) {
            $this->parser = new Engine;
        }
        return $this->parser;
    }

    /**
     * 
     * @api
     * @param   
     * @param    
     * @return
     * @link
     */
    public function getCollection ()
    {
        return $this->getParser()->getCollection();   
    }

    /**
     * 
     * @api
     * @param mixed $results
     * @return mixed $results
     * @link
     */
    public function setResults ($results)
    {
        return $this->results = $results;
    }

    /**
     * 
     * @api
     * @param mixed $results
     * @return mixed $results
     * @link
     */
    public function mergeResults ($results)
    {
        $this->results = array_merge($this->results, $results);
        return $this->results;
    }

    /**
     * 
     * @api
     * @return mixed $files
     * @link
     */
    public function getResults ()
    {
        return $this->results;
    }

    public function parseFiles($file)
    {
        $res = [];
        if(empty($file)) {
            return false;
        }
        if(is_array($file)) {
            foreach($file as $unitFile) {
                $this->parseFiles($unitFile);
            }
        }
        if(is_file($file)) {
            if($this->isHtml($file)) {
                $content = file_get_contents($file);
                $parser = $this->getParser();
                $result = $parser->parse($content);
                $res[$file] = [
                    "results" => $result,
                    "collection" => $parser->getCollection()
                ];
                $parser->clear();
            } else if($this->isZip($file)) {
                $zip = new ZipArchive;
                $zip->open($file);
                $files = [];
                $baseDir = $this->getBaseDir();
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $files[] = $baseDir . $zip->getNameIndex($i);
                }
                $zip->extractTo($baseDir);
                $zip->close();
                foreach($files as $unitFile) {
                    $this->parseFiles($unitFile);
                }
            } else {

            }
        }
        $this->mergeResults($res);
        return $this->getResults();
    }

    private function getMimeType($file)
    {
        //新しいPHPでは使えなくなったmime_content_type、一番便利なのに
        if(function_exists('mime_content_type')) {
            $mimetype = \mime_content_type($file);
        } else if(function_exists('finfo_open')) {
            //インストールしないと使えないfinfo_open
            $finfo = finfo_open(FILEINFO_MIME);
            $mimetype = finfo_file($finfo, $file);
            finfo_close($finfo);
        } else if($mime = \shell_exec('file -bi ' . \escapeshellcmd($file))) {
            //仕方ありません、shellの力を借ります
            $mime = \trim($mime);
            $mime = \preg_replace("/ [^ ]*/", "", $mime);
            $mimetype = \preg_replace('/;$/', "", $mime);
        } else {
            //shellでも無理なら、拡張子を見る
            $ext = pathinfo($file, PATHINFO_EXTENSION);
            $mimetypes = [
                'txt' => 'text/plain',
                'htm' => 'text/html',
                'html' => 'text/html',
                'php' => 'text/html',
                'css' => 'text/css',
                'js' => 'application/javascript',
                'json' => 'application/json',
                'xml' => 'application/xml',
                'png' => 'image/png',
                'jpe' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'jpg' => 'image/jpeg',
                'gif' => 'image/gif',
                'bmp' => 'image/bmp',
                'ico' => 'image/vnd.microsoft.icon',
                'tiff' => 'image/tiff',
                'tif' => 'image/tiff',
                'zip' => 'application/zip',
                'cab' => 'application/vnd.ms-cab-compressed',
                'pdf' => 'application/pdf',
            ];
            $mimetype = $mimetypes[$ext];
        }
        return $mimetype;
    }

    private function isHtml($file)
    {
        $mimetype = $this->getMimeType($file);
        return $mimetype === "text/html";
    }

    private function isZip($file)
    {
        $mimetype = $this->getMimeType($file);
        return $mimetype === "application/zip";
    }

    /**
     * 
     * @api
     * @param mixed $baseDir
     * @return mixed $baseDir
     * @link
     */
    public function setBaseDir ($baseDir)
    {
        return $this->baseDir = $baseDir;
    }

    /**
     * 
     * @api
     * @return mixed $baseDir
     * @link
     */
    public function getBaseDir ()
    {
        if ($this->baseDir === null) {
            $this->baseDir = dirname(__FILE__) . "/base/";
        }
        return $this->baseDir;
    }

    /**
     * 
     * @api
     * @param mixed $targetDir
     * @return mixed $targetDir
     * @link
     */
    public function setTargetDir ($targetDir)
    {
        return $this->targetDir = $targetDir;
    }

    /**
     * 
     * @api
     * @return mixed $targetDir
     * @link
     */
    public function getTargetDir ()
    {
        if ($this->targetDir === null) {
            //do samething here if we need;
        }
        return $this->targetDir;
    }
}