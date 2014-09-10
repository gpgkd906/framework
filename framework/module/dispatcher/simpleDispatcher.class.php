<?php
/**
 * 分发器(非rewrite版本,通过app和request参数指定路径以及生成链接)
 * 库中并不使用map表，但为了兼容myDispatcher，第一个参数必须留给map表
 * simpleDispatcher的另外一个意义在于提供一个最简单的例子告诉用户如何去使用dispatcher的interface。
 * 禁止实例化以及继承
 *   Licensed under the MIT license:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 *   author: chenhan,gpgkd906@gmail.com
 *   website: http://dev.gpgkd906.com/MyProject/
 */
require_once My::interfaces."myDispatcher.interface.php";

final class simpleDispatcher implements dispatcherInterface {

  private function __construct(){
    //close __construct
  }

  public static function map($map,$req,$logic,$tpl){
    if(self::isValidReq($_GET)){
      return self::build();
    }else{
      throw new Exception("不正なアクセスを検出しました\n\rアクセスは{$_SERVER['REMOTE_ADDR']}来たものです");
    }
  }
  
  public static function isValidReq($get){
    $req=$get["req"];
    $reqP=explode("/",$req);
    if(in_array("..",$reqP)){
      return false;
    }
    $request=$get["request"];
    $reqP=explode("/",$request);
    if(in_array("..",$reqP)){
      return false;
    }    
    return true;
  }

  public static function build($app=null){
      $request=array();
      if(empty($_GET["req"])){
	$app=My::app."index.php";
	$tpl="index.tpl";
	$appController="index";
	$appRequest="index";
      }else{
	$appController=$_GET["req"];
	$appRequest=empty($_GET["request"])?"index":$_GET["request"];
	$app=My::app.$_GET["req"].".php";
	if(empty($_GET["request"])){
	  $_GET["request"]="index";
	}
	$tpl=$_GET["req"]."/".$_GET["request"].".tpl";
      }
      $request["app"]=$app;
      $request["appController"]=$appController."MyController";
      $request["appRequest"]=$appRequest;
      $request["view"]=null;
      $request["logic"]=null;
      if(is_file($app)){
	$request["logic"]=$app;
      }
      if(is_file(My::template.$tpl)){
	$request["view"]=$tpl;
      }
      return $request;
  }

  public static function link($controller=null,$request=null,$param=array()){
    $uri=My::baseurl;
    $_queryString=array();
    if(!empty($controller)){
      $_queryString[]="app=".rawurlencode($controller);
    }
    if(!empty($request)){
      $_queryString[]="request=".rawurlencode($request);
    }
    if(!empty($param)){
      foreach($param as $k=>$v){
	$_queryString[]=rawurlencode($k)."=".rawurlencode($v);
      }
    }
    if(!empty($_queryString)){
      $queryString=join("&",$_queryString);
      $uri=$uri."?".$queryString;
    }
    echo $uri;
  }
}