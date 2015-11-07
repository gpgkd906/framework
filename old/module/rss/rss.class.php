<?php
/**
 *    RSS聚合自动生成(如果不使用模板的话)
 *    通过make接口可以迅速生成简单的rss聚合内容
 *      $rssMaker=new rss(title,link,description);
 *      $rss=$rssMaker->make($items);  //生成rss
 *      echo $rss
 *
 *    通过sitemap接口则可以自动生成网站的sitemap,由于sitemap会对全站进行彻底扫描，因此应该使用它进行静态生成。
 *   Licensed under the MIT license:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 *   author: chenhan,gpgkd906@gmail.com
 *   website: http://dev.gpgkd906.com/MyProject/
 */

class rss{
  protected $encode="UTF-8";
  protected $title="";
  protected $item="";
  protected $rss;
  protected $domain;
  protected $sitemap;
  protected $tree;
  protected $priority="0.60";
  protected $changefreq="weekly";

  public function __construct($title,$link,$description="NONE DESCRIPTION"){
    $this->title=join("",array(
			       "<title>",$title,"</title>\n",
			       "<link>",$link,"</link>\n",
			       "<description>",$description,"</description>\n"
			       )
		      )
    $this->domain=$link;
  }

  public function set_encode($encode){
    $this->encode=$encode;
  }

  public function make($items){
    $this->item($items);
    return $this->body();
  }
  
  public function sitemap($data){
    $this->smash($data);
    $this->build();
    return $this->sitemap_body();
  }

  protected function smash($items){
    $new=array();
    $parent=$this->domain;
    foreach($items as $key=>$item)
      {
	$new[$key]=$item['link'];
      }
    $this->sitemap=$new;
  }
  
  protected function build(){
    $arr=array();
    foreach($this->sitemap as $child){
      $arr[]=$this->parse_child($child);
    }
    $this->item=join('',$arr);
  }
  
  protected function parse_child($child){
    $data=join("",array(
			"<url>\n",
			"<loc>",htmlspecialchars($child,ENT_QUOTES),"</loc>\n",
			"<priority>",$this->priority,"</priority>\n",
			"<changefreq>",$this->changefreq,"</changefreq>\n",
			"</url>\n",
			)
	       );
    return $data;
  }


  protected function item($items){
    $arr=array();
    foreach($items as $item)
      {
	$arr[]="<item>\n";
	foreach($item as $name=>$value)
	  {
	    $arr[]="<".$name.">".htmlspecialchars($value,ENT_QUOTES)."</".$name.">\n";
	  }
	$arr[]="</item>\n";
      }
    $this->item=join("",$arr);
  }

  protected function body(){
    $this->rss=join("",array(
			     "<?xml version='1.0' encoding='",$this->encode,"' ?>\n",
			     "<rss version='2.0'>\n",
			     "<channel>",$this->title,$this->item,"</channel>\n",
			     "</rss>\n"
			     )
		    );
    return $this->rss;
  }

  protected function sitemap_body(){
    $sitemap=join("",array(
			   "<?xml version='1.0' encoding='",$this->encode,"' ?>\n",
			   "<urlset xmlns='http://www,sitemaps,org/schemas/sitemap/0.9'>\n",
			   $this->item,
			   "</urlset>",
			   )
		   );
    return $sitemap;
  }
}