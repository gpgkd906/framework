<?php
/**
 * 分发器(rewrite版本,pretty Url生成器)
 * 构造器需要传入map表，逻辑文件基本路径以及模板文件基本路径，以用于对URL和QUERY的分析
 * @return $request=array(
 *			     "app"=>*,
 *			     "logic"=>*,
 *			     "tpl"=>*,
 *			     "appController"=>*,
 *			     "appRequest"=>*,
 *			     );
 * 禁止实例化以及继承,对于无法使用rewriteEngine的环境，可以利用simpleDispatcher来实现无缝迁移
 * 同样可以通过构造一个相同interface的分发器来取代本分发器，分发器可以在config环境中注册
 *   Licensed under the MIT license:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 *   author: chenhan,gpgkd906@gmail.com
 *   website: http://dev.gpgkd906.com/MyProject/
 */
require_once My::interfaces."myDispatcher.interface.php";

final class myDispatcher implements dispatcherInterface {
  private static $map;
  private static $param=array();
  private static $request;
  private static $url;
  private static $logic;
  private static $view;
  private static $req;
  private static $app;

  private function __construct(){
    //close __construct
  }

  public static function map($map,$req,$logic,$view){
    self::$map=$map;
    self::$req=$req;
    self::$logic=$logic;
    self::$view=$view;
    unset($_GET[My::actionTag]);
    return self::parse();
  }
  
  public static function fetchRequest(){
    return self::$request;
  }
  
  private static function parse(){
    $req=self::$req;
    if(empty($req)){
      $req="/";
    }
    if( isset(self::$map[$req]) ){
      $app=self::$map[$req];
    }elseif($app=self::searchPattern($req)){
      self::$request["param"]=$req;
    }else{
      $_app=explode("/",$req);
      $app[]=array_shift($_app);
      $app[]=isset($_app[0][0])?array_shift($_app):"index";
      self::$request["param"]=$_app;
    }
    if(isset(self::$request["param"])){
      self::$request["param"]=self::parseParam(self::$request["param"]);
    }
    if($app=="/"){
      $app=array(
                 "index",
                 "index"
                 );
    }
    if(!self::isValidReq($app)){
      $except = "不正なアクセスは検出しました\n\r";
      $except+= "アクセスは{$_SERVER['REMOTE_ADDR']}からです。\n\r";
      $except+= "アクセス時間は".date("Y/m/d H:i:s",$_SERVER['REQUEST_TIME'])."になります\n\r";
      $except+= "今回のアクセスは管理者が調査ようにため記録されました。"
        throw new Exception($except);
    }
    self::$app=$app;
  }
  
  private static function searchPattern($req){
    foreach(self::$map as $key=>$app){
      if(preg_match(">^".$key."$>",$req,$m)){
	return $app;
      }
    }
  }
  
  /**
   * !!!deprecated
   */
  public static function build($app){
    $app["controll"]=$app[0];
    $app["page"]=$app[1];
    $logic=self::$logic.$app["controll"].".php";
    $tpl1=join("/",array($app[0],$app[1])).".tpl";
    if($app[1]==="index"){
      $tpl2=$app["controll"].".tpl";
    }else{
      $tpl2=null;
    }
    self::$request["app"]=$app?$app:'index';
    self::$request["logic"]=is_file($logic)?$logic:null;
    self::$request["view"]=is_file(self::$tpl.$tpl1)?$tpl1:(is_file(self::$tpl.$tpl2)?$tpl2:null);
    self::$request["appController"]=$app["controll"]."MyController";
    self::$request["appRequest"]=$app["page"];
    return self::$request;
  }
  
  public static function getAppController(){
    if(!self::$request["appController"]){
      self::$request["appController"]=self::$app[0]."MyController";
    }
    return self::$request["appController"];
  }
  
  public static function getAppName(){
    return self::$app[0];
  }
  
  public static function getPage(){
    return self::$app[1];
  }
  
  public static function getLogic(){
    if(!self::$request["logic"]){
      $logic=self::$logic.self::$app[0].".php";
      self::$request["logic"]=is_file($logic)?$logic:null;
    }
    return self::$request["logic"];
  }
  
  public static function getTpl(){
    if(!self::$request["tpl"]){
      $td=self::$view->getTemplateDir();
      $tpl1=join("/",array(self::$app[0],self::$app[1])).".tpl";
      if(self::$app[1]==="index"){
        $tpl2=self::$app[0].".tpl";
      }else{
        $tpl2=null;
      }
      self::$request["tpl"]=is_file($td.$tpl1)?$tpl1:(is_file($td.$tpl2)?$tpl2:null);
    }
    return self::$request["tpl"];
  }
  
  public static function getParam(){
    return self::$request["param"];
  }
  
  public static function isValidReq($app){
    if($app[0]===".." || $app[1]===".."){
      return false;
    }
    return true;
  }
  
  public static function parseParam($param){
    $new=array();
    foreach($param as $p){
      list($key,$val)=explode(":",$p);
      $new[$key]=$val;
    }
    return $new;
  }
  
  public static function link($controller=null,$request=null,$param=array()){
    $_uri=array();
    if($controller!==null){
      $_uri[]=$controller;
      if($request!==null){
        $_uri[]=$request;
      }
    }
    $uri=My::baseurl.join("/",$_uri);
    if(!empty($param)){
      $_queryString=array();
      foreach($param as $k=>$v){
        $_queryString[]=rawurlencode($k).":".rawurlencode($v);
      }
      $queryString=join("/",$_queryString);
      $uri=$uri."/".$queryString;
    }
    return $uri;
  }
}