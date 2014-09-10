<?php
/**
 * 标签云
 * 传入标签数据数组，至少应该包含url和关键词。
 * 最简单的数据至少应当构成以下形式(即最少也需包括链接和关键词)：
 * $data=array(
 *             array(
 *                   'url'=>$url_1,
 *                   'keyword'=>$keyword_1,
 *                   ),
 *             array(
 *                   'url'=>$url_2,
 *                   'keyword'=>$keyword_2,
 *                   ),
 *             ......,
 *             ......,
 *             )
 * 如果需要进行比较高度的设定，例如说，传入level参数等，则只需要在数组中加入相应项目即可。
 * 默认可自动设定class,level及color。
 * 支持自定义模板
 * 使用方法：
 * $tagcloud=new tagcloud();
 * $tagcloud->load($data); //设置对象数据
 * $tagcloud->style($style); //方式来自定义模板
 * 模板模式请参考系统默认$style，即
 * <a href='{url}'><span class='{class} {level}' style='color:{color};'>{keyword}</span></a>
 * $tagcloud->attr($option,$value); //对模板标签进行通用设置，但该方法不会覆盖数组中原有数据。
 * $tagcloud->force($option,$value); //对模板标签强制设定通用选项，该方法会覆盖原有数据。
 * $tagcloud->fetch();    //返回处理后的数据
 * $tagcloud->display();   //显示处理后的数据
 */
class tagcloud {

  protected $store=null;
  protected $tag=null;
  protected $cloud=null;
  protected $style="<a href='{url}'><span class='{class} {level}' style='color:{color};'>{keyword}</span></a>";
  
  public function __construct(){
    preg_match_all('/{(\w++)}/',$this->style,$match);
    $this->tag=array_combine($match[1],$match[0]);
    $this->var['class']='tagCloud';
  }

  public function load($data){
    shuffle($data);
    $this->store=$data;
    $this->cloud=null;
    return $this;
  }
  
  public function style($style){
    preg_match_all('/{(\w++)}/',$style,$match);
    $this->tag=array_combine($match[1],$match[0]);
    $this->style=$style;
    return $this;
  }

  private function parse(){
    $this->modify();
    $tpl=$this->style;
    $cloud=null;
    foreach($this->store as $item){
      $tpl=$this->style;
      foreach($this->tag as $tag=>$subject){
	if(isset($item[$tag])){
	  $tpl=str_replace($subject,$item[$tag],$tpl);
	}else{
	  $tpl=str_replace($subject,"",$tpl);
	}
      }
      $cloud[]=$tpl;
    }
    $this->cloud=$cloud;
  }
  
  public function fetch(){
    if(!$this->cloud){
      $this->parse();
    }
    return $this->cloud;
  }

  public function display(){
    if(!$this->cloud){
      $this->parse();
    }
    if(!empty($this->cloud)){
      echo join("&nbsp; ",$this->cloud);
    }
  }


  public function attr($tag,$value){
    $this->$tag=$value;
    return $this;
  }

  public function force($tag,$value){
    if(isset($this->tag[$tag])){
      $this->style=str_replace($this->tag[$tag],$value,$this->style);
    }
    return $this;
  }

  protected function modify(){
    $var=$this->var;
    $data=$this->store;
    foreach($var as $name=>$option){
      foreach($data as $key=>$item){
	if(!isset($item[$name])){
	  $data[$key][$name]=$option;
	}
      }
    }
    $this->store=$data;
  }
}


?>