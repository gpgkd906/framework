<?php
/**
 * chemplate PHP模板引擎
 *
 * <pre>
 * 关于模板引擎：
 * 1.1.0版本开始，loader和compiler就分离成两个相对独立的类，从而分离出编译与读取行为，减少了每次实例化时所消耗的资源。
 *
 * 细节与用法:
 *   概要：本模板是作为上位框架的一部分而实现各种处理
 *
 * 一:用法
 *
 *********************           后台部分           ************************
 *   1.1:系统:
 *      $chemplate->assign("sample",$sample):变量注册。
 *      $chemplate->display("file.tpl"):模板注册
 *      $chemplate->clear_assign:变量销毁
 *         clear_assign():销毁所有已注册的变量，默认。
 *         clear_assign("key"):销毁指名变量。
 *         clear_assign(array("key1","key2","key3"...)):批量销毁变量。
 *
 *   1.2:系统设定：
 *
 *       系统标签的设置:
 *         $chemplate->left_delimiter='<{';
 *         $chemplate->right_delimiter='}>';
 *
 *       系统路径的设置:
 *         $chemplate->_cache_dir="cache/";
 *         $chemplate->_template_dir="templates/";
 *         $chemplate->_template_chen_dir="templates_chen/";
 *
 *       系统预约语(必要的时候可以调整此预约语以读取smarty模板):
 *         $chemplate->Reserve='chemplate'
 *         注意,自2.0版本起，本模板不再兼容smarty。
 *
 *       运作模式:
 *         开启/关闭缓存(默认关闭)　       :$chemplate->_cache=false;
 *         缓存寿命                     :$chemplate->_cache_lifetime=3600;
 *         开启/关闭强制编译模式(默认关闭)  :$chemplate->force_compile=false;
 *     ＊关于缓存模式(推荐):
 *         1,display方法传递第二参数也可开启缓存模式，$chemplate->display("file.tpl",true);
 *         2,include_cache，插入其他模板的缓存。
 *     ＊关于编译模式:
 *         1,每次编译都会删除对应模板的缓存。
 *         2,自动编译。
 *
 *   1.3:模板变量:
 *       1.命名:
 *             以$开头，只能使用英语字母，数字，_。
 *       2.变数名保护:
 *             如果给变量加上单引号，模板引擎将不会对这个变量进行任何解析。例如：$sample在正常情况下会被解析成$this->_vars["sample"],而'$sample'则会表示为$sample。
 *       3.变量保护(已废止):请使用assign对变量进行注册使用。
 *       4.预约变量:以下变量为模板预约变量，不需要注册即可直接使用。
 *              <{$chemplate.now}>,<{$chemplate.get.\w+}>,<{$chemplate.post.\w+}>,<{$chemplate.session.\w+}>
 *              <{$chemplate.server.\w+}>,<{$chemplate.cookie.\w+}>,<{$chemplate.const.\w+}>
 *
 *******************************           前端部分          ******************************
 *
 *   1.4:模板标签
 *       if:分歧，使用方法同smarty。
 *       for:使用方法 <{for $i in $obj}>
 *                     <{$i.name}>
 *                  <{/for}>
 *       range:使用方法 <{range:name 1 to 100 by 10}>
 *                       <{$range.name}>
 *                    也可以省略name,此时则可以根据index来进行访问
 *                    例如 <{range to 300 by 50}>
 *                        <{$range.2}>
 *                    专门进行数字递进循环处理，可嵌套
 *       section:使用方法基本同smarty，但需注意，部分smarty用法已经分散给for和range方法，可用子变量：index,index_prev,index_next,show。
 *       include:插入其他模板<{include file="file.tpl"}>
 *       include_cache：插入其他模板的缓存<{include_cache file="file.tpl"}>
 *       components:静态插入，用于创建模块化的静态组件。和include_cache不同的是，该方法会把静态内容直接编译至主文件。此外可以使用的模板资源也远远不如include_cache。
 *       extends:模板继承。
 *              用法:  假设有模板b.tpl,其内容为
 *                     <{extends file='a.tpl'}>
 *                     <block:name>bbbb</block>
 *                    则b.tpl会继承a.tpl的所有内容，并替换掉其中<block:name>aaaa</block>的部分，如果有的话。
 *       fetch:获取模板内容但不输出 $chemplate->fetch("mail.tpl",[$cache]);加上cache flag可以直接读取cache.
 *
 *   1.5:可选参数(option):
 *       1.escape： $sample|escape:'html'；    转义变量，html,htmlall,url,urldecode。
 *       2.date_format： $sample|date_format:'Y年m月d日'；  对时间进行格式化，遵循PHP标准。
 *       3.count_words： $sample|count_words；  计算全文单词数量
 *       4.count_the_word:$sample|count_the_word:"测试"；计算指定文字出现次数，支持中文。
 *       5.truncate:$sample|truncate:5:"---"；截断文章多余长度，默认为80字，省略部分替换为"..."
 *       6.replace: $sample|replace:"subject":"replacement"； 置换文章内容
 *       7.count: $sample|count；  计算数组单元个数。
 *       8.str_repeat: $sample|str_repeat:n；  重复内容。
 *       9.允许单变量多选项: $sample|sre_repeat:3|strtoupper|escape:html
 *       10.var_dump。
 *
 *   1.6:空变量：
 *       空变量不会导致报错。
 *
 *   1.7:插件系统
 *       插件系统采用方法登录模式。
 *       首先，手动或者使用程序自动在模板中添加钩子。<{hook plugin='$trigger'}>
 *       然后，在系统上定义动作方法，并命名为$function
 *       最后，$chemplate->register_trigger($trigger,array($function,$param))，把方法注册到对应的钩子上。
 *       和smarty不同，本模板的插件系统不会改变其自身的行为。
 *       就本质而言，是上位框架插件系统在模板层的具体实现。
 *   1.8:扩展(相当于Smarty的插件系统)
 *       addTag,动态扩展模板标签以及标签处理方法，注意，当前版本只允许扩展单行标签。
 *
 * 二:维护:
 *   1.目标：除虫，提速。
 *   2.已知bug：变数选项中可以使用preg_replace，但不能在正则表达中使用‘|’，会导致模板出错。
 *             该bug是由于‘|’已用于分割变数选项，如在正则式中出现，会导致引擎无法正确解析变数选项。
 *             维护上曾尝试依赖添加‘\’转义符来避免不当解析，但这会导致编译后的正则式本身出错。
 *             因此目前版本模板引擎不支持在正则表达式中使用‘|’。
 *
 * log:
 * 
 * 01/10/2013:增加components标签，引入子模板系统，允许在模板中直接静态内容。ver 2.2.0
 * 12/21/2012:block标签中增加parent::block变数，可引用父模板对应block的内容。ver 2.1.2
 * 10/25/2012:删除section标签,ver 2.1.1
 * 10/24/2012:增加扩展,现在通过addTag方法可以动态扩展模板引擎支持的标签 ver 2.1.0
 * 09/25/2012:修正option的bug, ver 2.0.1
 * 07/26/2011:模板引擎整体升级为chemplate ver2.0
 * 07/26/2011:重写变量解析等方法,取消对[]的支持，从此所有变量统一使用以下写法:$country.stat.town.street
 * 07/26/2011:增加extends模板继承标签。
 * 07/25/2011:增加for标签和range标签，完全重写section方法，提高效率并消除对HTML5的冲突
 * 07/25/2011:删除镜像模式,删除foreach标签,放弃对smarty的兼容性。
 * 03/23/2011:添加插件功能。   (ver 1.3.0)
 * 03/23/2011:改善编译效率。(ver 1.2.2)
 * 03/23/2011:设定镜像模式为默认模式 (ver 1.2.1)
 * 03/01/2011:添加镜像模式。(ver 1.2.0)
 * 01/10/2011:分离编译器与读取器。(ver 1.1.0)
 * 01/07/2011:1.0.0版本完成。
 * 11/xx/2010:完成基本骨架。(ver ～0.2.0)
 * </pre>
 *
 *   Licensed under the MIT license:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 *   author: chenhan,gpgkd906@gmail.com
 *   website: http://dev.gpgkd906.com/MyProject/
 */
namespace Module\Chemplate;

use Module\Chemplate\Compiler;

class Chemplate {
	protected $_vars = array();
	public $Reserve = 'chemplate';
	public $_cache = false;
	public $force_compile = false;
	public $_cache_lifetime = 3600;
	public $left_delimiter = '<{';
	public $right_delimiter = '}>';
	public $_cache_dir = './cache/';
	public $_template_dir = './templates/';
	public $_template_chen_dir = './templates_chen/';
	protected $plugin;
	protected $rese;
	protected $ld;
	protected $rd;
	protected $cd;
	protected $td;
	protected $tcd;
	protected $compiler_plugin = array();
	protected $tplName = "";
	protected $tplExtension = "tpl";

	public function __construct(){
		$this->rese = &$this->Reserve;
		$this->ld = &$this->left_delimiter;
		$this->rd = &$this->right_delimiter;
		$this->cd = &$this->_cache_dir;
		$this->td = &$this->_template_dir;
		$this->tcd = &$this->_template_chen_dir;
		$this->_vars[$this->rese] = array();
		$this->_vars[$this->rese]['server'] = &$_SERVER;
		$this->_vars[$this->rese]['post'] = &$_POST;
		$this->_vars[$this->rese]['get'] = &$_GET;
		$this->_vars[$this->rese]['session'] = &$_SESSION;
		$this->_vars[$this->rese]['cookie'] = &$_COOKIE;
		$this->_vars[$this->rese]['now'] = &$_SERVER['REQUEST_TIME'];
	}

	/**
	 *interface:用户界面
	 *@assign:注册变量
	 *@display:注册模板
	 *@fetch:读取模板内容而不输出
	 *@clear_assign:销毁已注册变量
	 */

	final public function assign($name,$var){
		if( !empty($name) )$this->_vars[$name] = $var;
		return $this;
	}

	final public function assign_array($arr,$arr2 = null){
		if(!empty($arr2)){
			$arr = array_combine($arr,$arr2);
		}
		foreach($arr as $key = >$val){
			$this->assign($key,$val);
		}
	}
  
	public function assigned($name = null){
		if(empty($name)){
			return $this->_vars;
		}else{
			return isset($this->_vars[$name]) ? $this->_vars[$name] : null;
		}
	}

	public function display($file,$cache = false){
		//error_reporting(-9);
		if(is_file($this->td.$file)){
			$pathinfo = pathinfo($file);
			$this->tplName = $pathinfo["filename"];
			$this->tplExtension = $pathinfo["extension"];
			if($this->force_compile){
				$compiler = new Compiler($this);
				$compiler->setPlugin($this->compiler_plugin);
				require $newfile = $compiler->compile($file);
				if(is_file($cache_file = $this->cd.$this->get_cache($file)))$this->clean_cache($cache_file);
				$compiler = null;
			}elseif($cache||$this->_cache){
				readfile($this->load_cache($file));
			}else{
				require $this->load_php($file);
			}
		}else{
			echo 'error:没有发现模板文件'.$file.'，请确认该文件是否存在';
		}
	}

	final public function fetch($file,$cache = false){
		if(is_file($this->td.$file)){
			ob_start();
			//error_reporting(-9);
			if($cache){
				require $this->load_cache($file);
			}else{
				require $this->load_php($file);
			}
			//error_reporting(-1);
			$output = ob_get_contents();
			ob_end_clean();
			return $output;
		}else{
			echo 'error:没有发现模板文件'.$file.'，请确认该文件是否存在';
		}
	}

	public function clear_assign($vars = null){
		if(empty($vars)){
			$this->_vars = array();
		}elseif(is_array($vars)){
			foreach($vars as $value)
				{
					unset($this->_vars[$value]);
				}
		}else{
			unset($this->_vars[$vars]);
		}
		return $this;
	}

	final protected function include_display($file){
		if(is_file($this->td.$file))
			{
				require $this->load_php($file);
			}
		else
			{
				echo 'error:没有发现模板文件'.$file.'，请确认该文件是否存在';
			}
	}

	final protected function include_cache_display($file){
		if(is_file($this->td.$file))
			{
				require $this->load_cache($file);
			}
		else
			{
				echo 'error:没有发现模板文件'.$file.'，请确认该文件是否存在';
			}
	}


	/**
	 *缓存模式
	 *@load_cache：检查缓存文件。
	 *@creat_cache：创建缓存文件。
	 *@clean_cache:清除缓存。
	 */

	final protected function load_cache($file){
		$file_cache = $this->cd.str_replace(array('/','.'.$this->tplExtension),array('_','.html'),$file);
		$cache_exist = false;
		if(is_file($file_cache)){
			if( (filemtime($file_cache)+$this->_cache_lifetime) > $_SERVER['REQUEST_TIME']){
				return $file_cache;
			}
		}
		return $this->creat_cache($file,$file_cache);
	}

	final protected function creat_cache($file,$cache){
		ob_start();
		//error_reporting(-9);
		include_once $this->load_php($file);
		//error_reporting(-1);
		$cache_txt = ob_get_contents();
		ob_end_clean();
		file_put_contents($cache,$cache_txt);
		return $cache;
	}
  
	final public function get_cache($file){
		return str_replace(array('/','.'.$this->tplExtension),array('_','.html'),$file);
	}

	final public function clean_cache($cache){
		if(is_file($cache))unlink($cache);
	}

	/**
	 *普通模式
	 *@load_php:检查tpl是否更新,以及php文件是否存在
	 */
	final protected function load_php($file){
		$file_path = $this->tcd.'php_'.preg_replace('/\.[a-zA-Z]++$/','.php',str_replace("/","_",$file));
		if(is_file($file_path) && !$this->force_compile ){
			if(filemtime($file_path) > filemtime($this->td.$file)){
				return $file_path;
			}
		}
		$compiler = new Compiler($this);
		$compiler->setPlugin($this->compiler_plugin);
		$phpfile = $compiler->compile($file);
		if(is_file($cache_file = $this->cd.$this->get_cache($file)))$this->clean_cache($cache_file);
		$compiler = null;
		return $phpfile;
	}

	//debug
	public function debug(){
		echo "<pre>";
		var_dump($this->_vars);
		return $this;
	}

	//注册插件方法呼出
	public function trigger($trigger){
		if(isset($this->plugin[$trigger]))
			{
				foreach($this->plugin[$trigger] as $handler)
					{
						call_user_func_array($handler[0],$handler[1]);
					}
			}
	}

	//插件方法注册，允许多重注册
	public function register_trigger($trigger,$handler){
		if(!is_array($handler)){
			$handler = array($handler,array());
		}else{
			if(!isset($handler[1])){
				$handler[1] = array();
			}elseif(!is_array($handler[1])){
				$_handler = array(array_shift($handler));
				$_handler[1] = $handler;
				$handler = $_handler;
			}
		}
		$this->plugin[$trigger][] = $handler;
		return $this;
	}

	public static function makedefault($var,$option){
		return isset($var[0])?$var:$option;
	}

	public function addTag($tag,$call){
		$this->compiler_plugin[$tag] = $call;
	}
}
