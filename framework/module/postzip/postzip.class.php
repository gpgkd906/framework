<?php
/**
 *
 *   Licensed under the MIT license:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 *   author: chenhan,gpgkd906@gmail.com
 *   website: http://dev.gpgkd906.com/MyProject/
 */
class postzip {
  private static $dbs="myPostZip.db";
  private static $dbtype="sqlite";
  private static $dns=null;
  private static $user=null;
  private static $pass=null;
  private static $db;
  private static $tbs="my_postzip";
  private static $prepared=false;
  private static $cache;
  private static $jpost="http://www.post.japanpost.jp/zipcode/dl/kogaki/zip/";
  private static $all="ken_all.zip";
  
  public function __construct(){
	  //$this->cache=controller::getSingletonInstance()->getInstance("module")->getCache();
  }

  public function useMysql($dns,$user,$pass){
    self::$dbtype="mysql";
    self::$dns=$dns;
    self::$user=$user;
    self::$pass=$pass;
  }

  public function find_by_zip($cond){
    $cacheKey="myPostZip_zip_".rawurlencode($cond);
    //$res=$this->cache->get($cacheKey);
    if(empty($res)){
      self::prepareDbs();
      $cursor=self::$db->prepare("select * from ".self::$tbs." where zipcode like ? or old_zipcode like ?");
      $cond=$cond."%";
      $cursor->execute(array($cond,$cond));
      $res=$cursor->fetchall(2);
      //$this->cache->set($cacheKey,$res,86400);
    }
    return $res;
  }
  
  public function findByTown($cond){
    $cacheKey="myPostZip_Town_".rawurlencode($cond);
    //$res=$this->cache->get($cacheKey);
    if(empty($res)){
      self::prepareDbs();
      $cursor=self::$db->prepare("select * from ".self::$tbs." where town like ? or town_kana like ?");
      $cond="%".$cond."%";
      $cursor->execute(array($cond,$cond));
      $res=$cursor->fetchall(2);
      //$this->cache->set($cacheKey,$res,86400);
    }
    return $res;
  }

  public function findByCity($cond){
    $cacheKey="myPostZip_City_".rawurlencode($cond);
    //$res=$this->cache->get($cacheKey);
    if(empty($res)){
      self::prepareDbs();
      $cursor=self::$db->prepare("select * from ".self::$tbs." where city like ? or city_kana like ?");
      $cond="%".$cond."%";
      $cursor->execute(array($cond,$cond));
      $res=$cursor->fetchall(2);
      //$this->cache->set($cacheKey,$res,86400);
    }
    return $res;
  }

  public function findByState($cond){
    $cacheKey="myPostZip_State_".rawurlencode($cond);
    //$res=$this->cache->get($cacheKey);
    if(empty($res)){
      self::prepareDbs();
      $cursor=self::$db->prepare("select * from ".self::$tbs." where state like ? or state_kana like ?");
      $cond="%".$cond."%";
      $cursor->execute(array($cond,$cond));
      $res=$cursor->fetchall(2);
      //$this->cache->set($cacheKey,$res,86400);
    }
    return $res;  
  }
  
  //↓↓↓↓static
  private static function prepareDbs(){
    if(self::$prepared){
      return;
    }
    if(self::$dbtype==="sqlite"){
		self::$db=new PDO("sqlite:postzip");
		$checkTable="select name from sqlite_master where name='".self::$tbs."'";
    }elseif(self::$dbtype==="mysql"){
		self::$db=new PDO(self::$dns,self::$user,self::$pass);
		self::$db->query("set names utf8");
		$checkTable="show tables like '".self::$tbs."'";
    }
    $inited=false;
    foreach(self::$db->query($checkTable) as $row){
      $inited=true;
    }
    if(!$inited){
      self::install();
    }else{
      self::update();
    }
    self::$prepared=true;
  }

  public static function install(){
      self::createTable();
      self::fill();
  }

  public static function createTable(){
    if(self::$dbtype==="sqlite"){
      $sql="create table if not exists ".self::$tbs." ("
	."id integer primary key,"
	."code text,"
	."old_zipcode text,"
	."zipcode text,"
	."state_kana text,"
	."city_kana text,"
	."town_kana text,"
	."state text,"
	."city text,"
	."town text,"
	."flg1 text,"
	."flg2 text,"
	."flg3 text,"
	."flg4 text,"
	."flg5 text,"
	."flg6 text"
	.")";
    }else{
      $sql="create table if not exists ".self::$tbs." ("
	."id int(11) not null auto_increment,"
	."code text,"
	."old_zipcode text,"
	."zipcode text,"
	."state_kana text,"
	."city_kana text,"
	."town_kana text,"
	."state text,"
	."city text,"
	."town text,"
	."flg1 text,"
	."flg2 text,"
	."flg3 text,"
	."flg4 text,"
	."flg5 text,"
	."flg6 text,"
	."primary key (id)"
	.")";
    }
    self::$db->query($sql);
  }
  
  private static function fill(){
    set_time_limit(0);
    $csv=self::getAndUncompress(self::$all);
    self::_update($csv);
    unlink($csv);
  }

  private static function update(){
    
  }
 
  private static function getAndUncompress($file){
    $content=file_get_contents(self::$jpost.$file);
    file_put_contents("./ken_all.zip",$content);	
    $zip = new ZipArchive();
    $zip->open("./ken_all.zip");
    $csv=$zip->getNameIndex(0);
    $zip->extractTo("./");
    $zip->close();
    unlink("ken_all.zip");
    return "./" . $csv;
  }

  private static function _update($csv){
	  $getcsv=fopen($csv,"r");
	  $up=self::$db->prepare("insert into ".self::$tbs." (code,old_zipcode,zipcode,state_kana,city_kana,town_kana,state,city,town,flg1,flg2,flg3,flg4,flg5,flg6) values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
	  while($row=fgetcsv($getcsv)) {
		  $up->execute(self::convert($row));
	  }
	  fclose($getcsv);
  }
  
  private static function convert($arr){
    foreach($arr as $key => $val) {
      $arr[$key] = mb_convert_encoding($val, "UTF-8", "SJIS");
    }
    return $arr;
  }

}