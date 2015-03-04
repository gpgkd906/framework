<?php

class http_request {
  const POST=1;
  const GET=2;
  const PUT=3;
  const DELETE=4;
  private $request=array(
			 "http"=>array()
			 );
  private $uri;
  private $type;
  private $resource=array();
  
  public function request($data=array(),$type){
    switch($type){
    case self::POST:
      $this->setMethod("POST");
      break;
    case self::GET:
      $this->setMethod("GET");
      $this->type="get";
      break;
    case self::PUT:
      $this->setMethod("PUT");
      break;
    case self::DELETE:
      $this->setMethod("DELETE");      
      break;
    default:
      //do nothing
      break;
    }
    $this->request["http"]["content"]=http_build_query($data);
    return $this;
  }
  
  public function uri($uri){
    $this->uri=$uri;
    return $this;
  }
  
  public function execute(){
    if($this->type==="get"){
      $this->uri=$this->uri."?".$this->request["http"]["content"];
    }
    if(!$this->get("content-type")){
      $this->setContent_type("text/plain");
    }
    $context=stream_context_create($this->request);
    $response=file_get_contents($this->uri,null,$context);
    return $response;
  }
  
  public function __call($method,$args){
    if(strpos($method,"set")===0){
      $name=str_replace("set","",strtolower($method));
      array_unshift($args,$name);
      call_user_func_array(array($this,"set"),$args);
    }
    return $this;
  }
  
  public function set($name,$value){
    $name=str_replace("_","-",$name);
    $this->request["http"][$name]=$value;
    return $this;
  }

  public function get($name){
    //    $name=strtoupper($name);
    return $this->request["http"][$name];
  }
  
}