<?php
namespace Module\Download;
/**
 *  下载库
 *   Licensed under the MIT license:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 *   author: chenhan,gpgkd906@gmail.com
 *   website: http://dev.gpgkd906.com/MyProject/
 */
class Download {
  protected $buffer=null;
  protected $file=null;
  protected $action=null;

  public function name($name){
    header('Content-Disposition: attachment; filename="'.$name.'"');
    header('Content-Type: application/octet-stream');
    header('Content-Transfer-Encoding: binary');
    return $this;
  }
  
  public function file($filepath){
    header('Content-Length: '.filesize($filepath));
    $this->file=$filepath;
    $this->action='file';
    return $this;
  }

  public function buffer($buffer){
    header('Content-Length: '.strlen($buffer));
    $this->buffer=$buffer;
    $this->action='buffer';
    return $this;
  }
  
  public function start(){
    if($this->action='file'){
      readfile($this->file);
    }elseif($this->action='buffer'){
      echo $this->buffer;
    }
  }
  
}