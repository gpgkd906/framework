<?php
/**
 *
 *   Licensed under the MIT license:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 *   author: chenhan,gpgkd906@gmail.com
 *   website: http://dev.gpgkd906.com/MyProject/
 */
class google_map{
  private $version="3";
  private $api_key;
  private $api_url="http://maps.google.com/maps/api/{type}?sensor={sensor}&language={language}";
  private $geo_url="http://maps.googleapis.com/maps/api/geocode/json?address={address}&sensor={sensor}&language={language}";
  private $type="js";
  private $sensor="false";
  private $language="ja";
  private $putCacheHandler;
  private $getCacheHandler;
  private $cache;
  private $cache_prefix="googleMap";
  private $cache_expire=86400;

  public function __construct(){
    $this->cache=array();
    $this->putCacheHandler=array($this,"defaultPutCacheHandler");
    $this->getCacheHandler=array($this,"defaultGetCacheHandler");
  }

  public function setType($type){
    $this->type=$type;
  }
  
  public function getGeoByAddr($addr){
    $cacheName=$this->cache_prefix."addr".$addr;
    $res=$this->getCache($cacheName);
    if(!$res){
      $res=file_get_contents(str_replace("{address}",rawurlencode($addr),str_replace("{sensor}",$this->sensor,str_replace("{language}",$this->language,$this->geo_url))));
      $this->putCache($cacheName,$res);
    }
    return json_decode($res,true);
  }
  
  public function putCache($name,$cache){
    return call_user_func_array($this->putCacheHandler,array($name,$cache));
  }

  public function getCache($name){
    return call_user_func_array($this->getCacheHandler,array($name));
  }
  
  public function defaultPutCacheHandler($name,$data){
    $this->cache[$name] = $data;
  }

  public function defaultGetCacheHandler($name){
    return $this->cache[$name];
  }
  
  public function setPutCacheHandler($handler){
    $this->putCacheHandler=$handler;
  }

  public function setGetCacheHandler($handler){
    $this->getCacheHandler=$handler;
  }
}

