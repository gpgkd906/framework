<?php
/**
 * logger
 *   Licensed under the MIT license:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 *   author: chenhan,gpgkd906@gmail.com
 *   website: http://dev.gpgkd906.com/MyProject/
 */
abstract class myFileLogger {
  private $file;
  private $log=array();
  private $accessTime;
  
  public function __construct(){
    $this->accessTime=date("Y-m-d H:i:s",$_SERVER["REQUEST_TIME"]);
  }

  public function setLogfile($file){
    if(!is_file($file)){
      touch($file);
    }
    if(!is_writable($file)){
      throw new Exception("指定したファイル:${file}は書き込めません，ファイルシステムの権限をチェックしてください");
    }
    if( filesize($file) > 1024*1024 ){
      rename($file,$file.".".date("ymd").".bak");
      touch($file);
    }
    $this->file=$file;
  }
  
  public function write(){
    $content=array("");
    foreach($this->log as $log){
      foreach($log as $name=>$val){
	$content[]=$name.":".$val;
      }
      $content[]="";
    }
    file_put_contents($this->file,join("\n",$content),FILE_APPEND);
    $this->log=array();
  }

  public function info($msg,$write=false){
    $this->log[]=array(
		       'accessTime'=>$this->accessTime,
		       'logTime'=>microtime(true),
		       'type'=>'info',
		       'message'=>$msg,
		       );
  }

  public function fatal($msg){
    $this->log[]=array(
		       'accessTime'=>$this->accessTime,
		       'logTime'=>microtime(true),
		       'type'=>'fatal',
		       'message'=>$msg,
		       );
  }
  public function except($msg){
    $this->log[]=array(
		       'accessTime'=>$this->accessTime,
		       'logTime'=>microtime(true),
		       'type'=>'except',
		       'message'=>$msg,
		       );
  }

  public function error($msg){
    $this->log[]=array(
		       'accessTime'=>$this->accessTime,
		       'logTime'=>microtime(true),
		       'type'=>'error',
		       'message'=>$msg,
		       );
  }

  public function warn($msg){
    $this->log[]=array(
		       'accessTime'=>$this->accessTime,
		       'logTime'=>microtime(true),
		       'type'=>'waring',
		       'message'=>$msg,
		       );
  }
  
  public function notice($msg){
    $this->log[]=array(
		       'accessTime'=>$this->accessTime,
		       'logTime'=>microtime(true),
		       'type'=>'notice',
		       'message'=>$msg,
		       );
  }

  public function display(){
    $args=func_get_args();
    echo "<pre>";
    foreach($args as $arg){
      print_r($arg);
    }
    echo "</pre>";
  }
  
}
