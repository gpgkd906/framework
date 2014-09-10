<?php
/**
 *
 *
 *   Licensed under the MIT license:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 *   author: chenhan,gpgkd906@gmail.com
 *   website: http://dev.gpgkd906.com/MyProject/
 */
final class chemplate_compiler {
	protected $ld;
	protected $rd;
	protected $cd;
	protected $td;
	protected $tcd;
	protected $plugin=array();
	protected $option_table=array(
		'replace'=>'str_replace',
		'date_format'=>'date',
		'reg_replace'=>'preg_replace',
		'isset'=>'isset',
		'empty'=>'empty',
		'is_array'=>'is_array',
		'join'=>'join',
		'implode'=>'join',
	);

	public function __construct($engine){
		$this->rese=&$engine->Reserve;
		$this->ld=&$engine->left_delimiter;
		$this->rd=&$engine->right_delimiter;
		$this->cd=&$engine->_cache_dir;
		$this->td=&$engine->_template_dir;
		$this->tcd=&$engine->_template_chen_dir;
		$this->engine=&$engine;
	}
  
	/**
	 *编译模式
	 */

	public function compile($file,$write=true){
		$text=file_get_contents($this->td.$file);
		if(strpos($text,$this->ld.'extends')!==false)$text=$this->method_extends($text);
		if(strpos($text,$this->ld.'/block')!==false)$text=$this->method_block($text);
		if(strpos($text,$this->ld.'components')!==false)$text=$this->method_components($text);
		$text=$this->convert_comment($text);
		if(strpos($text,$this->ld.'/if')!==false)$text=$this->method_if($text);
		if(strpos($text,$this->ld.'/for')!==false)$text=$this->method_for($text);   
		if(strpos($text,$this->ld.'/range')!==false)$text=$this->method_range($text);
		if(strpos($text,$this->ld.'include')!==false)$text=$this->method_include($text);
		if(strpos($text,$this->ld.'hook')!==false)$text=$this->method_hook($text);
		if(strpos($text,$this->ld.'/php')!==false)$text=$this->method_php($text);
		if(strpos($text,$this->ld.'assign')!==false)$text=$this->method_assign($text);
		//bootstrap shortcut
		if(strpos($text,$this->ld.'boot')!==false)$text=$this->method_bootstrap($text);
		$text=$this->extension($text);
		$text=$this->convert_vars($text);
		$text=trim($text);
		//生成php文件,转录tpl文件路径
		if(!$write){ return $text; }
		if(!is_dir($this->tcd))
			{
				mkdir($this->tcd);
			}
		$file=$this->tcd.'php_'.preg_replace('/\.[a-zA-Z]++$/','.php',str_replace("/","_",$file));
		if( file_put_contents($file,$text) === false){
			throw new Exception("chemplate can not write file:".$file);
		}
		return $file;
	}
  
	/**
	 *置换所有注释，默认注释风格为 {开始模板标签* xxxx *关闭模板标签}
	 */
	protected function convert_comment($text){
		return preg_replace('/'.$this->ld.'\*[\s\S]*?\*'.$this->rd.'/S','',$text);
	}

	/**
	 *置换所有模板变数及模板标签
	 */
	protected function convert_vars($text){
		$text=preg_replace('/'.$this->ld.'((?:[\'"])?\\$.+?)'.$this->rd.'/S','<?php echo $1 ?>',$text);
		$text=str_replace(array($this->rd,$this->ld),array(' ?>','<?php '),$text);
		$text=preg_replace_callback('/\\$(?<![\']\\$)(\w++(?:[\:\.]\w++)*)((?:\|[^|\s]++)*)/S',array(&$this,'template_var'),$text);
		//变量名保护
		$text=preg_replace('/\'(\\$.*?)\'(?<!\[\')(?!\])/S','$1',$text);
		$text=str_replace('@@','$',$text);
		return $text;
	}

	protected function template_var($match){
		$option[1]=$option[2]="";
		if(isset($match[2][1])){
			$option=$this->option($match[2]);
			$option["1"]=preg_replace_callback('/\\$(?<![\']\\$)(\w++(?:[\:\.]\w++)*)((?:\|[^|\s]++)*)/S',array(&$this,'template_var'),$option["1"]);
			$option["2"]=preg_replace_callback('/\\$(?<![\']\\$)(\w++(?:[\:\.]\w++)*)((?:\|[^|\s]++)*)/S',array(&$this,'template_var'),$option["2"]);
		}
		if(!isset($option['default'])){      $option['default']='';    }
	  
		$set=explode(':',$match[1]);
		if(isset($set[1]) && $set[1]=='const'){
			return $option['1'].$set[2].$option['default'].$option['2'];
		}
		$target = $option['1'].'$this->_vars[\''.join('\'][\'',$set).'\']'.$option['default'].$option['2'];
		$target = str_replace(".", "->", $target);
		$target = preg_replace("/((->\w++)*)'\]/S", '\']$1',$target);
		return $target;
	}
  
	protected function option($str){
		$options=array_reverse(explode('|',$str));
		$default=$le=$re="";
		foreach($options as $vars){
			$ops=explode(':',$vars);
			$fp_len=count($ops);
			switch($ops[0]){
				case "escape":
				case "h":
					if(isset($ops[1])){
						switch($ops[1]){
							case "'html'":case '"html"':
								$le.='htmlspecialchars(';
								$re=',ENT_QUOTES)'.$re;
								break;
							case "'htmlall'":case '"htmlall"':
								$le.='htmlentities(';
								$re=',ENT_QUOTES)'.$re;
								break;
							case "'url'":case '"url"':
								$le.='rawurlencode(';
								$re=')'.$re;	
								break;
							default:break;
						}
					}else{
						$le.='htmlspecialchars(';
						$re=',ENT_QUOTES)'.$re;
					}
				break;
				case "unescape":
					if(isset($ops[1])){
						switch($ops[1]){
							case "'html'":case '"html"':
								$le.='htmlspecialchars_decode(';
								$re=',ENT_QUOTES)'.$re;
								break;
							case "'htmlall'":case '"htmlall"':
								$le.='html_entity_decode(';
								$re=',ENT_QUOTES)'.$re;
								break;
							case "'url'":case '"url"':
								$le.='rawurldecode(';
								$re=')'.$re;	
								break;
							default:break;
						}
					}else{
						$le.='htmlspecialchars(';
						$re=',ENT_QUOTES)'.$re;
					}	
					break;
				case "count_words":
					$le.='count(preg_split("/[\s,;.]/",';
					$re='))'.$re;
					break;
				case "count_the_word":
					if(isset($ops[1])){
						$le.='mb_substr_count(';
						$re=','.$ops[1].')'.$re;
					}
					break;
				case "truncate":
					$offset=isset($ops[1])?(int)$ops[1]:80;
					$replacement=isset($ops[2])?$ops[2]:"'...'";
					$le.='mb_strimwidth(';
					$re=',0,'.$offset.','.$replacement.',\'utf-8\')'.$re;
					break;
				case "default":
					$str='';
					for($i=1;$i<$fp_len;$i++){
						$str.=','.$ops[$i];
					}
					$le.='chemplate::makedefault(';
					$re=$str.')'.$re;
					break;
				default:
					if(isset($this->option_table[$ops[0]])){
						$str='';
						for($i=1;$i<$fp_len;$i++){
							$str.=$ops[$i].',';
						}
						$le.=$this->option_table[$ops[0]].'('.$str;
						$re=')'.$re;
					}elseif( class_exists('Func') && method_exists('Func',$ops[0]) ){
						$str='';
						for($i=1;$i<$fp_len;$i++){
							$str.=','.$ops[$i];
						}
						$le.='Func::'.$ops[0].'(';
						$re=$str.')'.$re;
					}elseif( function_exists($ops[0]) ){
						$str='';
						for($i=1;$i<$fp_len;$i++){
							$str.=','.$ops[$i];
						}
						$le.=$ops[0].'(';
						$re=$str.')'.$re;
					}
			}
		}
		return array('1'=>$le,'2'=>$re,'default'=>$default);
	}

	/**
	 * if方法的匹配与置换
	 */
	protected function method_if($text){
		$pattern="/".$this->ld."(?:(?>\/?)if|else(?:if)?).*?".$this->rd."/S";
		preg_match_all($pattern,$text,$match);
		foreach($match[0] as $key=>$strings){
			$ori=$strings=str_replace(array($this->rd,$this->ld),array("",""),$strings);
			if($strings==='/if'){
				$strings=str_replace('/if','}',$strings);
			}elseif($strings==='else'){
				$strings=str_replace('else','}else{',$strings);
			}else{
				$strings=str_replace('else if','}elseif',$strings);
				$strings=str_replace(';','',$strings);
				$strings=str_replace('if','if(',$strings);
				$strings.=" ){";
			}
			$text=str_replace($this->ld.$ori.$this->rd,$this->ld.$strings.$this->rd,$text);
		}
		return $text;
	}
  
	/**
	 * for方法的匹配与置换
	 *@支持多重for
	 */
	protected function method_for($text){
		preg_match_all('/'.$this->ld.'for\s+\\$(\w++)\s+in\s+\\$(\w+(?:\.\w+)*)'.$this->rd.'/S',$text,$block);
		foreach($block[0] as $key=>$tpl){
			$item=$block[1][$key];
			$obj1=$block[2][$key];
			$obj2=str_replace('.','"]["',$obj1);
			$replace=$this->ld.'if(!empty($'.$obj1.')){'
				.'foreach($'.$obj1.' as $'.$this->rese.'["for"]["'.$obj2.'"]["index"]=>$'.$item.'){'.$this->rd;
			$text=str_replace($tpl,$replace,$text);
		}
		$text=str_replace($this->ld.'/for'.$this->rd,$this->ld.'}}'.$this->rd,$text);   
		return $text;
	}
  
	/**
	 * range方法
	 */
	protected function method_range($text){
		preg_match_all("/".$this->ld."range(?::(\w+))?\s+(?:(\d+)\s+)?to\s+(\d+)\s*(?:by\s+(\d+))?\s*".$this->rd."/",$text,$block);
		foreach($block[0] as $key=>$tpl){
			$name=$block[1][$key]?"@@this->_vars['range']['".$block[1][$key]."']":"@@this->_vars['range']";
			$start=$block[2][$key];
			$end=$block[3][$key];
			$step=$block[4][$key]?$block[4][$key]:1;
			$replace=$this->ld
				."foreach(range($start,$end,$step) as $name){"
				.$this->rd;
			$text=str_replace($tpl,$replace,$text);
		}
		$text=str_replace("/range","}",$text);
		return $text;
	}

	/**
	 * include方法
	 * 将来极其可能被components所代替
	 */
	protected function method_include($text){
		if(substr_count($text,"include_cache")){
			$text=preg_replace("/include_cache file=(\S+?)\s*".$this->rd."/S","@@this->include_cache_display($1);".$this->rd,$text);
		}
		if(substr_count($text,"include")){
			$text=preg_replace("/include file=(\S+?)\s*".$this->rd."/S","@@this->include_display($1);".$this->rd,$text);
		}
		return $text;
	}
  
	protected function method_extends($text,$pblocks=array()){
		$block=array();
		if(substr_count($text,'/block')){
			preg_match_all("/".$this->ld."block:([\w\.]++)".$this->rd."([\s\S]*?)".$this->ld."\/block".$this->rd."/",$text,$match);
			$block=array_combine($match[1],$match[2]);
		}
		$newblock=array_merge($block,$pblocks);
		foreach($newblock as $key=>$val){
			if(isset($pblocks[$key])){
				if(substr_count($val,$this->ld.'parent::block'.$this->rd)){
					$newblock[$key]=str_replace($this->ld.'parent::block'.$this->rd,$block[$key],$newblock[$key]);
				}
			}
		}
		preg_match("/".$this->ld."extends\s+(?:file=)?['\"](\S+?)['\"]".$this->rd."/",$text,$match);
		$extends=$match[1];
		if(is_file($this->td.$extends)){
			$text=file_get_contents($this->td.$extends);
		}else{
			die("错误:模板继承不存在对象文件");
		}
		if(substr_count($text,$this->ld.'extends')){
			return $this->method_extends($text,$newblock);
		}else{
			if(substr_count($text,'/block')){
				preg_match_all("/".$this->ld."block:([\w\.]++)".$this->rd."([\s\S]*?)".$this->ld."\/block".$this->rd."/",$text,$match);
				$block=array_combine($match[1],$match[2]);
				foreach($block as $key=>$b){
					if(isset($newblock[$key])){
						if(substr_count($newblock[$key],$this->ld.'parent::block'.$this->rd)){
							$newblock[$key]=str_replace($this->ld.'parent::block'.$this->rd,$block[$key],$newblock[$key]);
						}
						$text=str_replace($this->ld."block:".$key.$this->rd.$b,$this->ld."block:".$key.$this->rd.$newblock[$key],$text);
					}
				}
			}
			return $text;
		}
	}

	protected function method_block($text){
		$text=preg_replace("/".$this->ld."block:([\w\.]++)".$this->rd."/","",$text);
		$text=str_replace($this->ld.'/block'.$this->rd,"",$text);
		return $text;
	}

	/**
	 *　替换模板中的钩子
	 */
	protected function method_hook($text){
		$text = preg_replace("/hook(?: type=auto)? trigger=['\"]([^'\"]++)['\"]/S","@@this->trigger('$1')",$text);
		return preg_replace("/hook\s++['\"]?(\S++)['\"]?\s*/","@@this->trigger('$1')",$text);
	}

	/**
	 * PHP tag
	 */
	protected function method_php($text){
		preg_match_all("/".$this->ld."php".$this->rd."([\s\S]*?)".$this->ld."\/php".$this->rd."/",$text,$match);
		foreach($match[0] as $tpl){
			$php=str_replace("$","@@",$tpl);
			$php=str_replace($this->ld."php".$this->rd,"<?php ",str_replace($this->ld."/php".$this->rd," ?>",$php));
			$text=str_replace($tpl,$php,$text);
		}
		return $text;
	}

	/**
	 * assign tag
	 */
	protected function method_assign($text){
		preg_match_all("/".$this->ld."assign\s++([\s\S]+?)".$this->rd."/S",$text,$match);
		foreach($match[0] as $key=>$tpl){
			list($var,$val)=preg_split("/\s+/",$match[1][$key]);
			$var=preg_replace("/^[\"']|[\"']$/","",str_replace("var=","",$var));
			$val=preg_replace("/^[\"']|[\"']$/","",str_replace("value=","",$val));
			if(strpos($val,"$")===0){
				$text=str_replace($tpl,"<?php @@this->assign('".$var."',".$val."); ?>",$text);	
			}else{
				$text=str_replace($tpl,"<?php @@this->assign('".$var."',\"".$val."\"); ?>",$text);
			}
		}
		return $text;
	}

	/**
	 * components
	 * 实验性追加tag
	 * 与extends/block相反，该tag则是将外部模板静态化“嵌入”目标模板，处理优先度低于extends/block
	 */
	protected function method_components($text){
		//preg_match_all("/".$this->ld."components:([\w\.\/]+)([\w\\\$=\s]+?)?".$this->rd."/S",$text,$match);
		preg_match_all("/".$this->ld."components:([\w\.\/]+)".$this->rd."/S",$text,$match) or preg_match_all("/".$this->ld."components\s+['\"](\S+?)['\"]".$this->rd."/S",$text,$match);
		$componTX=array();
		foreach($match[0] as $key=>$original){
			$fname=$match[1][$key];
			$name=str_replace("/","_",$fname);
			$components=isset($componTX[$name])?$componTX[$name]:"";
			if($components===""){
				$target=$this->td."components/".$fname;
				if(is_file($target)){
					$components=file_get_contents($target);
					$componTX[$name]=$components;
				}
			}
			$text=str_replace($original,$components,$text);
		}
		return $text;
	}

	protected function method_bootstrap($text){
		preg_match_all("/".$this->ld."bootstrap:(\w+)([\s\S]*?)".$this->rd."/S",$text,$m);
		if(!empty($m)){
			chemplate_bootstrap::tag($this->ld,$this->rd);
		}
		foreach($m[0] as $key=>$source){
			$tag=$m[1][$key];
			$option=parse_ini_string(str_replace(" ","\r\n",trim($m[2][$key])));
			if(isset($option["selector"][0])){
				$option["selector"]=" id='{".$option["selector"]."}' ";
			}
			if(method_exists("chemplate_bootstrap",$tag)){
				$text=str_replace($source,call_user_func_array("chemplate_bootstrap::".$tag,$option),$text);
			}else{
				$text=str_replace($source,"",$text);
			}
		}
		return $text;
	}

  

	protected function extension($text){
		foreach($this->plugin as $tag=>$_call){
			if(is_callable($_call)){
				preg_match_all('/'.$this->ld."(".$tag."((?!".$this->rd.").*?))".$this->rd.'/S',$text,$matches);
				foreach($matches[0] as $key=>$pattern){
					$replacements=call_user_func_array($_call,array($matches[2][$key]));
					$text=str_replace($matches[1][$key],$replacements,$text);
				}
			}
		}
		return $text;
	}

	public function setPlugin($plugin){
		$this->plugin=$plugin;
	}

	//bootstrap extension
  
}

class chemplate_bootstrap {
	private static $ld="<{";
	private static $rd="}>";
	public static function tag($ld="<{",$rd="}>"){
		self::$ld=$ld;
		self::$rd=$rd;    
	}
  
	public static function stylesheet($tag="default"){
		$style="body {padding-top:40px;padding-bottom:40px;background-color:#f5f5f5;position:relative}";
		return $style;
	}
	/**
	   {bootstrap:warn message=message}
	   @message string
	*/
	public static function warn($message=null){
		$html='<div class="alert alert-block"><button class="close" data-dismiss="alert">&times;</button>'
			.'<strong>Warning!</strong>'.$message.'</div>';
		return $html;
	}

	public static function error($message=null){
		$html='<div class="alert alert-error alert-block"><button class="close" data-dismiss="alert">&times;</button>'
			.'<strong>Error!</strong>'.$message.'</div>';
		return $html;
	}
  
	public static function success($message=null){
		$html='<div class="alert alert-success alert-block"><button class="close" data-dismiss="alert">&times;</button>'
			.'<strong>Success!</strong>'.$message.'</div>';
		return $html;
	}
  
	/**
	   {bootstrap:label value=value status=status}
	*/
	public static function label($value,$status=""){
		if(strpos($value,"$")===0){
			$value=self::$ld.$value.self::$rd;
		}
		$html='<span class="label label-'.$status.'">'.$value.'</span>';
		return $html;
	}

	/**
	   {bootstrap:badge value=value status=status}
	*/
	public static function badge($value,$status=""){
		if(strpos($value,"$")===0){
			$value=self::$ld.$value.self::$rd;
		}
		$html='<span class="badge badge-'.$status.'">'.$value.'</span>';
		return $html;
	}

	/**
	   {bootstrap:searchForm name=name placeholder=placeholder}
	   @name string
	   @placeholder string
	*/
	public static function searchForm($name,$placeholder=null,$selector=""){
		$html='<form class="navbar-search pull-left"'.$selector.'><input name="'.$name.'" class="search-query" placeholder="'.$placeholder.'" type="text"/></form>';
		return $html;
	}

	/**
	   {bootstrap:progress value=value}
	   @value number/percent
	*/
	public static function progress($value=null,$selector=""){
		$html='<div class="progress"><div class="bar bar-success" style="width:'.$value.'%;"'.$selector.'></div></div>';
		return $html;
	}
  
	/**
	   {bootstrap:pageHeader title=title subtitle=subtitle}
	   @title string
	   @subtitle string
	*/
	public static function pageHeader($title,$subtitle){
		$html='<div class="page-header"><h1>'.$title.'<small>'.$subtitle.'</small></h1></div>';
		return $html;
	}
  
	/**
	   {bootstrap:menu link=$link display=display}
	   @link variables(array)
	   @display string/null
	*/
	public static function menu($link,$display="none"){
		$html="<{echo chemplate_bootstrap::action('menu',{$link},'{$display}')}>";
		return $html;
	}

	/**
	   {bootstrap:button button=$button size=size}
	   @button variables/string
	   @size string/null
	*/
	public static function button($button,$size="small"){
		if(strpos($button,'$')!==0){
			$button="'".$button."'";
		}
		$html="<{echo chemplate_bootstrap::action('button',{$button},'{$size}')}>";
		return $html;    
	}

	/**
	   {bootstrap:buttonmenu button=$button position=position}
	   @button variables
	   @position string
	*/
	public static function buttonmenu($button,$position="bottom,left"){
		$html="<{echo chemplate_bootstrap::action('buttonmenu',{$button},'{$position}')}>";
		return $html;        
	}

	/**
	   {bootstrap:tabs tabs=$tabs}
	   @tabs variables
	*/
	public static function tabs($tabs){
		$html="<{echo chemplate_bootstrap::actionTabs({$tabs})}>";
		return $html;        
	}
  
	/**
	   {bootstrap:breadcrumbs path=$path}
	   @path variables
	*/
	public static function breadcrumbs($path){
		$html="<{echo chemplate_bootstrap::action('breadcrumbs',{$path})}>";
		return $html;            
	}

	/**
	   {bootstrap:navbar nav=$nav active=active fix=fix}
	*/
	public static function navbar($nav,$active,$fix="top"){
		if(strpos($active,"$")!==0){
			$active="'".$active."'";
		}
		$html="<{echo chemplate_bootstrap::actionNavbar({$nav},{$active},'{$fix}')}>";
		return $html;            
	}

	public static function action(){
		$params=func_get_args();
		$tag=array_shift($params);
		$param=array_shift($params);
		switch($tag){
			case "menu":
				$display=array_shift($params);
				return self::actionMenu($param,$display);
				break;
			case "button":
				if(is_string($param)){
					$param=explode(",",$param);
				}
				if($size=array_shift($params)){
					$size="btn-".$size;
				}
				return self::actionButton($param,$size);
				break;
			case "buttonmenu":
				$position=explode(',',array_shift($params));
				if(in_array("top",$position)){
					$top="dropup";
				}
				if(in_array("right",$position)){
					$right="pull-right";
				}    
				return self::actionButtonMenu($param,$top,$right);
				break;
			case "tabs":
				return self::actionTabs($param);
				break;
			case "breadcrumbs":
				return self::actionBreadcrumbs($param);
				break;
			case "navbar":
				$active=array_shift($params);
				$fix=array_shift($params);
				return self::actionNavbar($param,$active,$fix);
				break;
			default:
				//do nothing
				break;
		}
	}

	public static function actionMenu($param,$display){
		$html='<div class="dropdown clearfix">'
			.'<ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu" style="display:'.$display.'">';
		foreach($param as $label=>$link){
			if(is_int($label)){
				$html.='<li class="divider"></li>';
			}else{
				$html.='<li><a tabindex="-1" href="'.$link.'">'.$label.'</a></li>';
			}
		}
		$html.='</ul></div>';
		return $html;
	}

	public static function actionButton($param,$size){
		$html='<div class="btn-group">';
		foreach($param as $p){
			$html.='<button class="btn '.$size.'">'.$p.'</button>';
		}
		$html.='</div>';
		return $html;
	}

	public static function actionButtonMenu($param,$top="",$right=""){
		$html="";
		foreach($param as $label=>$buttons){
			$html.='<div class="btn-group '.$top.'"><button class="btn">'.$label.'</button><button class="btn dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>';
			$html.='<ul class="dropdown-menu '.$right.'">';
			foreach($buttons as $button=>$href){
				$html.='<li><a tabindex="-1" href="'.$href.'">'.$button.'</a></li>';
			}
			$html.='</ul></div>';
		}
		return $html;
	}
  
	public static function actionTabs($param,$prefix="tabs"){
		$html='<div class="tabbable"><ul class="nav nav-tabs">';
		$cnt=1;
		foreach($param as $title=>$content){
			$active=$cnt===1?"active":"";
			$html.='<li class="'.$active.'"><a href="#'.$prefix.$cnt.'" data-toggle="tab">'.$title.'</a></li>';
			$cnt++;
		}
		$html.='</ul><div class="tab-content">';
		$cnt=1;
		foreach($param as $title=>$content){    
			$active=$cnt===1?"active":"";
			$html.='<div class="tab-pane '.$active.'" id="'.$prefix.$cnt.'"><p>'.$content.'</p></div>';
			$cnt++;
		}
		$html.='</div></div>';
		return $html;
	}

	public static function actionBreadcrumbs($path){
		$html='<ul class="breadcrumb">';
		$last=array_pop($path);
		foreach($path as $label=>$link){
			$html.='<li><a href="'.$link.'">'.$label.'</a><span class="divider">/</span></li>';
		}
		$html.='<li class="active">'.$last.'</li>';
		$html.='</ul>';
		return $html;
	}

	public static function actionNavbar($nav,$active,$fix="navbar-static-top"){
		if($fix==="top"){
			$fix="navbar-fixed-top";
		}elseif($fix==="bottom"){
			$fix="navbar-fixed-bottom";
		}
		$html='<div class="navbar navbar-inverse '.$fix.'"><div class="navbar-inner"><div class="container"><ul class="nav">';
		foreach($nav as $label=>$link){
			if($label===$active){
				$onActive="active";
			}else{
				$onActive="";
			}
			$html.='<li class="'.$onActive.'"><a href="'.$link.'">'.$label.'</a></li>';
		}
		$html.='</ul></div></div></div>';
		return $html;
	}

}

