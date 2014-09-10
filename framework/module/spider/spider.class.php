<?php
/**
 * Spider!!!Spam!!!but...python or ruby can do better
 */
class spider {
  protected $unmatch=array();
  protected $matched=array();
  protected $sitemap=array();
  protected $domain;
  protected $reg;
  protected $count=0;
  private $linkInfo=array();

  public function __construct(){
    $this->domain=null;
    $this->reg=null;
    error_reporting(0);
  }
  

  public function target($start){
    $this->unMatch[$start]=$start;
    $this->start=$start;
    $this->domain=$start;
    $this->reg='/'.preg_quote($start,'/').'/';
    return $this;
  }
  
  public function domain($domain){
    $this->domain=$domain;
    $this->reg='/'.preg_quote($domain,'/').'/';
    return $this;
  }
  
  public function userAgent($user_agent=null){
    $ua=$user_agent?$user_agent:'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0; Win64; x64; .NET CLR 2.0.50727; SLCC1; Media Center PC 5.0; .NET CLR 3.0.30618; .NET CLR 3.5.30729)';
    ini_set('user_agent',$ua);
    return $this;
  }
  
  public function netting(){
    $this->start($this->start);
    return $this->sitemap;
  }
  
  //start和spam互相递归，因此必须注意这两个方法需要保持其构造的简单性,以避免陷入循环黑洞
  //这两个方法的递归分为两个阶段，第一个阶段为收集阶段，这一个阶段中，实例通过访问不同的页面，收集不同的链接，并保存于链接库等待处理
  //第二个阶段是处理阶段，和第一个阶段并无明显分界线，但基本上，第二个阶段中基本上不会出现新的链接，因此引擎的主要工作变更为对链接库进行处理。
  //当链接库中所有链接处理完毕，整个递归也就抵达其终点了。
  protected function start($link){
    echo $link."\n\r";
    if($this->count++ > 500){
      return;
    }
    $parsed=$this->parse($link);
    $this->unMatch=array_unique( array_merge($this->unMatch,$parsed) );
    $this->spam();
  }

  protected function spam(){
    if(!count($this->unMatch)){
      return;
    }
    $target=array_rand($this->unMatch);
    $checkLink=$this->unMatch[$target];
    unset($this->unMatch[$target]);
    $this->start($checkLink);
  }
  

  protected function parse($link){
    $this->matched[]=$link;
    if(isset($this->unMatch[$link])){
      unset($this->unMatch[$link]);
    }
    //  get contents
    $page=file_get_contents($link);
    $page=preg_replace("/<!--[\s\S]*?-->/","",$page);
    file_put_contents("./cache/".str_replace(array("/./","/"),"_",str_replace($this->domain,"",$link)),$page);
    //  get title 
    //$title=$this->preg_title($page);
    //  get link
    $links=$this->preg_link($page);
    foreach($links as $l){
      $l=str_replace(array("/./","/"),"_",str_replace($this->domain,"",$l));
      $this->linkInfo[$l][]=$link;
    }
    //  diff link
    $links=array_diff($links,$this->matched);
    //  save
    $this->sitemap[]=array(
			   //'title'=>$title,
			   'link'=>$link,
			   );
    return $links;
  }
  

  protected function checkLink($link){
    if(preg_match($this->reg,$link)){
      return true;
    }
    return false;
  }

  protected function preg_title($text){
    preg_match('/<title>([^<>]++)<\/title>/',$text,$match);
    if(isset($match[1])){
      $title=$match[1];
    }else{
      $title=$this->domain;
    }
    return $title;
  }

  protected function preg_link($text){
    preg_match_all('/<a href=(["\'])([^"\']++)(?1)(?:(?!<\/a>).)*<\/a>/',$text,$match);
    $links=$this->filterLink($match[2]);
    return $links;
  }
  
  //本方法的两个作用：1，比较链接是否属于目标域名，2，删除同页面不同参数的重复链接。
  //换言之，仅仅通过对本方法的改造，可以实现对全网络的无差别搜索。
  protected function filterLink($links){
    $new=array();
    foreach($links as $key=>$link){
      $link=preg_replace("/PHPSESSID=[^&]*/","",$link);
      if(preg_match("/^#/",$link)){
	continue;
      }
      if(!preg_match("/^http/",$link)){
	$link=$this->domain.$link;
      }
      if(preg_match($this->reg,$link)){
	$new[]=$link;
	//$new[]=preg_replace('/[^\/]++$/','',$link);
      }
    }
    return array_unique($new);
  }

  public function putLinkInfo($file){
    foreach($this->linkInfo as $l=>$link){
      file_put_contents($file,"[ ".$l." ]".join("\n\r",$link)."\n\r\n\r",FILE_APPEND);
    }
  }

}