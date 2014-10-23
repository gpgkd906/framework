<?php

class util {
  private $isMobile=null;
  private $isSmart=null;
  
  //委托login
  public function login(){
    
  }

  /**
   * clearSession
   * 清除session
   */
  public function clearSession(){
    if (isset($_COOKIE[session_name()])) {
      setcookie(session_name(),'',time()-60*60*24*365, '/');
    }
    $_SESSION=array();
    session_unset();
    session_destroy();
  }

  /**
   *  重定向
   */
  public function redirect($to=""){
    if( strpos($to,"http://")===0 || strpos($to,"https://")===0 ){
      $url=true;
    }
    if($url){
      header('Location:'.$to);
    }else{
      header('Location:'.My::domain.$to);
    }
    die();
  }

  /**
   * tracking
   */
  public function tracking(){
    $access=array();
    if( isset($_SESSION["access"]) ){
      $access=$_SESSION['access'];
      $this->agent=$access["agent"];
    }else{
      $access=array();
      $agent=$_SERVER["HTTP_USER_AGENT"];
      $this->agent["mobile"]=preg_match("/DoCoMo|SoftBank|Vodafone|KDDI|WILLCOM|emobile/",$agent)?true:false;
      $this->agent["smartphone"]=preg_match("/Android|iPad|iPhone|iPod/",$agent)?true:false;
      $access["agent"]=$this->agent;
      $access["ip"]=$_SERVER["REMOTE_ADDR"];
    }
    $access["tracking"][]=$_SERVER["REQUEST_URI"];
    $access["time"][]=$_SERVER["REQUEST_TIME"];
    $_SESSION["access"]=$access;
  }

  public function isMb(){
    if($this->isMb===null){
      $agent=$_SERVER["HTTP_USER_AGENT"];
      $this->isMb=preg_match("/DoCoMo|SoftBank|Vodafone|KDDI|WILLCOM|emobile/",$agent)?true:false;      
    }
    return $this->isMb;
  }

  public function isSp(){    
    if($this->isSp===null){
      $agent=$_SERVER["HTTP_USER_AGENT"];
      $this->isSp=preg_match("/iPhone|iPod/",$agent)?true:false;
      if(!$this->isSp){
	$this->isSp=(preg_match("/Android/",$agent) && preg_match("/Mobile/",$agent))?true:false;      
      }
    }
    return $this->isSp;
  }

  public function isTp(){
    if($this->isTp){
      $agent=$_SERVER["HTTP_USER_AGENT"];
      $this->isTp=preg_match("/iPad/",$agent)?true:false;
      if(!$this->isTp){
	$this->isTp=(preg_match("/Android/",$agent) && !preg_match("/Mobile/",$agent))?true:false;      
      }
    }
    return $this->isTp;

  }

  /**
   *  检查请求是否本网域的子页面
   */
  public function isApp($url){
    return preg_match('/'.preg_quote(My::domain,'/').'/',$url);
  }

  /**
   * hash
   */
  public function hash($data,$type,$level){
    while($level --> 0){
      $data=hash($type,$data);
    }
    return $data;
  }

}