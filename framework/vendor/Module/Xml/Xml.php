<?php
/**
 *    该xml库主要是对simpleXML的一个包装,除了解析xml文件以外，更主要的是可以正确返回数组
 *    实际上这个库相当于一个xml解析器工厂，返回simplexml对象或者是对应的数组。
 *    返回的simplexml对象可以进行更多的xml相关处理.
 *    此外，本库提供了简单的增加节点，删除节点，修改节点的方法。
 *    用法：
 *    初始化
 *     $xml=new xml($xmlString);
 *     或者
 *     $xml=new xml();
 *     $xml->load($xmlString);
 *     或者
 *     $xml=new xml();
 *     $xml->file($xmlfile);
 *    返回xml对象
 *     $xml->fetch(1);
 *     或者
 *     $xml->fetch('xml');
 *    返回数组
 *     $xml->fetch(2);
 *     或者
 *     $xml->fetch('array');
 *    增删改
 *    增加节点
 *     $xml->add("app.test",1);在根路径下的app节点下增加test节点并赋值为1
 *    修改节点
 *     $xml->edit("app.test",2);对根路径下的app节点下的test节点赋值2
 *    删除节点
 *     $xml->del("app.test");删除根路径下的app节点下的test节点
 *   Licensed under the MIT license:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 *   author: chenhan,gpgkd906@gmail.com
 *   website: http://dev.gpgkd906.com/MyProject/
 */
namespace Module\Xml;

class Xml {
  private $file;
  
  public function fetch($type='array'){
    switch($type){
    case 'xml':
    case 1:
      return $this->xml;
      break;
    case 'array':
    case 2:
    default:
      return $this->xml2array($this->xml);
      break;
    }
  }
  
  public function load($xml){
    $this->xml=simplexml_load_string($xml);
    return $this;
  }
  
  public function file($file){
    $this->file=$file;
    if(is_file($file)){
      $this->xml=simplexml_load_file($file);
    }
    return $this;
  }
  
  public function url($netFile){
    $xml=file_get_contents($netFile);
    return $this->load($xml);
  }

  public function save($file=null){
    if($file==null){
      $file=$this->file;
    }
    return file_put_contents($file,$this->xml->asXML());
  }

  public function add($target,$value){
    $node=$this->searchNode($target);
    if(isset($node['node'])){
      $node['node']->addChild($node['target'],$value);
    }else{
      $this->xml->addChild($target,$value);
    }
  }

  public function edit($target,$value){
    $node=$this->searchNode($target);
    if(isset($node['node'])){
      $node['node']->{$node['target']}=$value;
    }else{
      $this->xml->{$target}=$value;
    }
  }

  public function del($target){
    $node=$this->searchNode($target);
    if(isset($node['node'])){
      $dom=dom_import_simplexml( $node['node']->{$node['target']} );
    }else{
      $dom=dom_import_simplexml( $this->xml->{$target} );
    }
  }
  
  private function searchNode($nodepath){
    $nodes=explode('.',$nodepath);
    $target=array_pop($nodes);
    $node=null;
    foreach($nodes as $name){
      if(isset($node)){
	if( !isset($node->{$name}) ){
	  $node->addChild($name,null);
	}
	$node=$node->{$name};
      }else{
	if( !isset($this->xml->{$name}) ){
	  $this->xml->addChild($name,null);
	}
	$node=$this->xml->{$name};
      }
    }
    if($node){
      $exists=isset($node->{$target});
    }else{
      $exists=isset($this->xml->{$target});
    }
    return array('node'=>$node,'target'=>$target,'exists'=>$exists);
  }

  public function hasNode($nodepath){
    $nodes=explode('.',$nodepath);
    $node=null;
    foreach($nodes as $name){
      if(isset($node)){
	if(isset($node->{$name})){
	  $node=$node->{$name};
	}else{
	  return false;
	}
      }elseif(isset($this->xml->{$name})){
	$node=$this->xml->{$name};
      }else{
	return false;
      }
    }
    return true;
  }

  public function asXML(){
    return $this->xml->asXML();
  }

  public function asJson(){
    return json_encode($this->fetch(2));
  }

  private function xml2array($xml){
    $arr = array();
    $keys = array();
    foreach($xml as $key => $node){
      if(!isset($keys[$key])) {
	$keys[$key] = 1;
	$arr[$key] = $this->xml2array($node);
      } elseif($keys[$key] > 1) {
	$arr[$key][] = $this->xml2array($node);
      } else {
	$keys[$key]++;
	$temp = $arr[$key];
	$arr[$key] = array();
	$arr[$key][] = $temp;
	$arr[$key][] = $this->xml2array($node);
      }
    }
    if(empty($arr)){
      $arr = trim((string)$xml);
    }
    return $arr;
  }
  
  /**
   * array2xml，本来应该用simplexmlelement对象和DOM对象相互作用进行处理。
   * 但是太麻烦，所以干脆用了另外一种比较粗暴而简便的方法，直写文本。
   */
  public function array2xml($root,$array){
    $xml = array("<?xml version='1.0' standalone='yes'?>");
    $xml[] = "<{$root}>";
    foreach($array as $key=>$node){
      $xml[] = $this->makeitem($key, $node);
    }
    $xml[] = "</{$root}>";
    $this->xml=new SimpleXMLElement(join("", $xml));
    return $this;
  }

  public function makeitem($key,$node){
    $subxml = array();
    $prefix = $afterfix = "";
    if(is_array($node)) {
      foreach($node as $index => $obj){
	if(is_numeric($index)){
	  $subxml[] = $this->makeitem($key, $obj);
	}else{
	  $prefix="<{$key}>";
	  $afterfix="</{$key}>";
	  $subxml[] = $this->makeitem($index, $obj);
	}
      }
    }else{
      $subxml[] = "<{$key}>";
      $subxml[] = $node;
      $subxml[] = "</{$key}>";
    }
    return $prefix . join("", $subxml) . $afterfix;
  }
  
}
