<?php

class gravatar {
  private $protocol="http://";
  private $gravatar='www.gravatar.com/avatar/';
  private $email;
  private $s=80;
  private $d="mm";
  private $r="g";

  public function setEmail($email){
    $this->email=$email;
  }
  
  public function setSize($size){
    $this->s=$size;
  }

  public function setDefault($default){
    $this->d=$default;
  }
  
  public function setRating($rating){
    $this->r=$rating;
  }
  
  public function enableSSL(){
    $this->protocol="https://";
  }
  
  public function disableSSL(){
    $this->protocal="http://";
  }

  public function url(){
    return $this->protocol.$this->gravatar.md5( strtolower( trim( $this->email ) ) ) . "?s=".$this->s."&d=".$this->d."&r=".$this->r;
  }

  public function saveAsFile($file){
    $url=$this->url();
    $content=file_get_contents($url);
    return file_put_contents($file,$content);
  }
  
}