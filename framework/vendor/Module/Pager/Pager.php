<?php
/**
 * 分页库
 *   Licensed under the MIT license:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 *   author: chenhan,gpgkd906@gmail.com
 *   website: http://dev.gpgkd906.com/MyProject/
 */
namespace Module\Pager;

class Pager {
  private $display_num = 10;
  private $items_num = null;
  private $max_pages = null;
  private $request_page=1;
  private $pages_length=5;
  private $pages=null;
  private $proto_url;
  private $replace='<{page}>';
  private $split='';
  private $frontStyle=null;
  private $backStyle=null;
  private $format=null;
  private $formatHandler;
  private $autoQuery=true;
  private $query=array();
  private $unQuery=array();
  
  public function __construct($request=1){
    $this->request(intval($request));
    $this->formatHandler=array($this,"defaultFormat");
  }

  final public function display_num($num){
    $this->display_num = intval($num);
    $this->calc_max();
  }

  final public function items_num($items_num) {
    $this->items_num = intval($items_num);
    $this->calc_max();
  }
  
  final private function calc_max(){
    if(isset($this->display_num, $this->items_num)) {
      $this->max_pages = ceil($this->items_num / $this->display_num);
    }    
  }

  final public function max($max){
    $this->max_pages=ceil($max);
    return $this;
  }
  
  final public function get_range() {
    if($this->request() > $this->max_pages) {
      $this->request($this->max_pages);
    }
    $request = $this->request();
    $start = ($request - 1) * $this->display_num;
    $end = $request * $this->display_num;
    if(isset($this->items_num) && $end > $this->items_num) {
      $end = $this->items_num;
    }
    return array($start, $end);
  }

  final public function get_start() {
    if($this->request() > $this->max_pages) {
      $this->request($this->max_pages);
    }
    $request = $this->request();
    return ($request - 1) * $this->display_num;
  }

  final public function get_end() {
    if($this->request() > $this->max_pages) {
      $this->request($this->max_pages);
    }
    $request = $this->request();
    $end = $request * $this->display_num;
    if(isset($this->items_num) && $end > $this->items_num) {
      $end = $this->items_num;
    }
    return $end;
  }

  /**
   * 设置或取得当前页数
   */
  final public function request($page=false){
    if($page){
      if($page < 0) {
	$page = 1;
      }
      $this->request_page=intval($page);
    }else{
      return $this->request_page;
    }
    return $this;
  }
  
  /**
   * 设置分页长度
   */
  final public function length($len){
    $this->pages_length=intval($len);
    return $this;
  }
  
  /**
   * @计划移除该方法
   */
  final public function wrap($front=null,$back=null){
    if($front)$this->frontStyle=$front;
    if($back)$this->backStyle=$back;
    return $this;
  }
  
  /**
   * 设置分页链接，默认参数下为当前页面，并自行生成queryString
   */
  final public function url($url="",$param=null,$split=null){
    if($param)$this->replace=$param;
    if($split)$this->split=$split;
    if($this->autoQuery){
		$this->query = array_diff_key(array_merge($_GET, $this->query), $this->unQuery);
		$this->query["page"]=$this->replace;
		if(strpos($url,"?")!==false){
			$tempUrl=explode("?",$url);
			$url=$tempUrl[0];
		}
		if(!empty($this->query)){
			$url=$url . "?" . $this->build_query($this->query);
		}
    }
    $this->proto_url=$url;
    return $this;
  }

  final public function format_url($page) {
    return str_replace($this->replace, $page, $this->proto_url);
  }

  private function build_query($query){
    $_q = array();
    foreach($query as $name=>$value){
      $_q[] = $name . "=" . $value;
    }
    return join("&",$_q);
  }
  /**
   * 关闭queryString自动生成
   */
  final public function disableQuery(){
    $this->autoQuery=false;
  }

  final public function disable_auto_query(){
    $this->disableQuery();
  }
  
  /**
   * 移除queryString变量
   */
  final public function removeQuery($key){
    $this->unQuery[]=$key;
  }

  final public function remove_query($key){
    return $this->removeQuery($key);
  }
  
  /**
   * 增加queryString变量
   */
  final public function addQuery($key,$value){
    $this->query[$key]=$value;
  }

  final public function add_query($key, $value){
    return $this->addQuery($key, $value);
  }
  
  /**
   * 分页计算
   */
  final public function flip(){
    $data["request"]=$this->request_page;
    if($this->max_pages<=$this->pages_length){
      //如果要求页数全部在可见范围之内。
      $data["first"]=1;
      $data["last"]=$this->max_pages;
      $data["pre"]=false;
      $data["next"]=false;
    }else{
      //要求页数总数超过可见范围，即存在pre（上一页）或者next（下一页），或者两者兼有
      $num=intval(ceil(($this->pages_length-1)/2));
      $first_temp=$this->request_page-$num;
      $last_temp=$this->request_page+$num;
      //可见范围内最后一页
      if($last_temp<$this->pages_length){
	$data["last"]=$this->pages_length;
	$data["next"]=$this->pages_length+1;
      }elseif($last_temp<$this->max_pages){
	$data["last"]=$last_temp;
	$data["next"]=$last_temp+1;	
      }else{
	$data["last"]=$this->max_pages;
	$data["next"]=false;
      }
      //可见范围内最初一页
      if($data["last"]===$this->max_pages){
	$data["first"]=$data["last"]-$num*2;
	$data["pre"]=$data["last"]-$this->pages_length;
      }elseif($first_temp>1){
	$data["first"]=$first_temp;
	$data["pre"]=$first_temp-1;
      }else{
	$data["first"]=1;
	$data["pre"]=false;
      }
    }
    $this->pages=$data;
    return $this;
  }

  //可通过回调函数自定义渲染方式
  private function format(){
    if(!$this->pages)$this->flip();
    //返回最终分页代码
    $this->format=call_user_func_array($this->formatHandler,array($this->pages));
  }
  
  public function setFormatHandler($handler){
    $this->formatHandler=$handler;
  }

  private function defaultFormat($pages){
    $flip=array('<ul class="pagination">');
    if($pages["request"]>1)
      {
		  $flip[] = '<li class="paginate_button previous disabled" aria-controls="dataTables-example" tabindex="0" id="dataTables-example_previous"><a href="' . str_replace($this->replace,($pages["request"]-1),$this->proto_url) . '">&#171; 前</a></li>';
      }
    for($i=$pages["first"];$i<=$pages["last"];$i++)
      {
        if($i == $pages["request"])
          {
			  $flip[] = '<li class="paginate_button active" aria-controls="dataTables-example" tabindex="0"><a href="javascript:void(0)">' . $i . '</a></li>'; 
          }
        else
          {
			  $flip[] = '<li class="paginate_button" aria-controls="dataTables-example" tabindex="0"><a href="' . str_replace($this->replace,$i,$this->proto_url) . '">' . $i . '</a></li>';
          }
      }
    if($pages["request"]<$pages['last'])
      {
		  $flip[] = '<li class="paginate_button next" aria-controls="dataTables-example" tabindex="0" id="dataTables-example_next"><a href="' . str_replace($this->replace,($pages["request"]+1),$this->proto_url) . '">次 &#187;</a></li>';
      }
	$flip[] = "</ul>";
    return join($this->split ,$flip);
  }
  
  /**
   *　返回FLIP数据，可用于模板。
   */
  public function get(){
    if(!$this->pages)$this->flip();
    return $this->pages;
  }
  
  /**
   * 直接输出通过回调函数渲染的分页样式
   */
  public function show(){
    if(!$this->proto_url)$this->url();
    if(!$this->format)$this->format();
    echo $this->format;
  }
  
  /**
   * 获取回调函数渲染的分页样式而不输出
   */
  public function fetch(){
    if(!$this->format)$this->format();
    return $this->format;
  }
  
  /**
   * 获取当前第一页
   */
  public function first(){
    return $this->pages['first'];
  }
  
  /**
   * 获取当前最后一页
   */
  public function last(){
    return $this->pages['last'];
  }
}