<?php

class view_helper extends helper_core {

	public $name;
	private $vars;
	private $flash = array();
	private $css = array();
	private $js = array();
	private $cdn = array();
	private $view_path = null;
	private $view_parts = null;
	private $ext = ".html";
	private $layout = null;
	private $page = null;
	private $action = null;
	
	public function __construct() {
		$this->view_parts = config::fetch("root") . "/framework/view_parts/";
	}

	public function get_action() {
		if($this->action === null) {
			$this->action = App::controller()->get_action();
		}
		return $this->action;
	}
	
	public function get_page() {
		if($this->page === null) {
			$this->page = App::controller()->get_page();
		}
		return $this->page;
	}

	public function set_name($name){
		$this->name = $name;
	}
	
	public function get_link(){
		$args = func_get_args();
		$www = config::fetch("www");
		return $www . join("/", $args);
	}

	public function use_layout($layout) {
		$this->layout = $layout;
	}

	public function layout($default) {
		if(!$this->layout){
			$this->layout = $default;
		}
		echo $this->layout;
	}
	
	public function get_static(){
		$args = func_get_args();
		$www = config::fetch("static");
		return $www . join("/", $args);
	}
	
	public function link(){
		$args = func_get_args();
		$link = call_user_func_array(array($this, "get_link"), $args);
		echo $link;
	}
	
	public function statics(){
		$args = func_get_args();
		$link = call_user_func_array(array($this, "get_static"), $args);
		echo $link;    
	}
	
	public function register_css() {
		$css = func_get_args();
		$this->css = array_merge($this->css, $css);
	}
	
	public function load_css($css = null) {
		if($css === null) {
			$this->css = array_unique($this->css);
			foreach($this->css as $css) {
				$css = $this->get_static("css", $css);
				echo '<link href="' . $css . '" rel="stylesheet">', PHP_EOL;
			}
		} else {
			$css = $this->get_static("css", $css);
			echo '<link href="' . $css . '" rel="stylesheet">', PHP_EOL;			
		}
	}
	
	public function register_js() {
		$js = func_get_args();
		$this->js = array_merge($this->js, $js);
	}
	
	public function load_js($js = null) {
		if($js === null) {
			$this->js = array_unique($this->js);
			foreach($this->js as $js) {
				$js = $this->get_static("js", $js);
				echo '<script src="' . $js . '"></script>', PHP_EOL;
			}		
		} else {
			$js = $this->get_static("js", $js);
			echo '<script src="' . $js . '"></script>', PHP_EOL;			
		}
	}

	public function register_cdn() {
		$cdn = func_get_args();
		$this->cdn = array_merge($this->cdn, $cdn);
	}

	public function load_cdn() {
		$this->cdn = array_unique($this->cdn);
		foreach($this->cdn as $cdn) {
			echo '<script src="' . $cdn . '"></script>', PHP_EOL;
		}		
	}
	
	public function truncate_with_br($str, $limit = 150){
		$lines = preg_split("/<br>|<br \/>|<br\/>/", $str);
		$new = array();
		while($limit > 0) {
			$line = array_shift($lines);
			if( $line === null) {
				break;
			}
			if(!isset($line[13])) {        
				$limit -= 14;
			} else {
				$len = strlen($line);
				if($len > $limit) {
					$line = mb_strimwidth($line, 0, $limit, "", "utf-8");
					$len = $limit;
				}
				$limit -= $len;
			}
			$new[] = $line;
		}
		return join("<br/>",$new);
	}
	
	public function truncate($str, $limit, $overfix = "...", $encode = "utf-8") {
		return mb_strimwidth($str, 0, $limit, $overfix, $encode);
	}
	
	public function date($format, $date){
		echo date($format, strtotime($date));
	}
	
	public function header($fix = null) {
		$this->parts("header", $fix);
	}
	
	public function footer($fix = null) {
		$this->parts("footer", $fix);
	}
	
	public function side($fix = null) {
		$this->parts("side", $fix);
	}
	
	//alias part => parts ,because there is some typing miss from coder...
	public function part($parts, $fix = null) {
		$this->parts($parts, $fix);
	}

	public function parts($parts, $fix = null) {
		if(!empty($fix)) {
			$parts = $parts . "-" . $fix;
		}
		$parts = $parts . $this->ext;
		$file = $this->view_parts . $parts;
		if(is_file($file)) {
			$this->_render($this->vars, $file);
		}
	}

	public function fetch_parts($vars, $parts) {
		$this->vars = $vars;
		ob_start();
		$parts = $parts . $this->ext;
		$file = $this->view_parts . $parts;
		if(is_file($file)) {
			$this->_render($vars, $file);
		}
		$output=ob_get_contents();
		ob_end_clean();
		return $output;
	}

	public function render($vars, $template){
		if(!isset($this->view_path)) {
			$this->view_path = App::path("view");
		}
		$template = $this->view_path . $this->name . "/" . $template . $this->ext;
		if(is_file($template)) {
			error_reporting(E_ALL ^ E_NOTICE);
			if(!isset($vars["now"])) {
				$vars["now"] = $_SERVER["REQUEST_TIME"];
			}
			$this->vars = $vars;
			$this->_render($vars, $template);
		}
	}
	
	private function _render($vars, $template) {
		$this->h($vars);
		if(is_array($vars)) {
			 extract($vars);
		}
		require $template;
	}
	
	public function get_defined_vars($key) {
		if(isset($this->vars[$key])) {
			return $this->vars[$key];
		}
		return null;
	}

	 public function google_map_resize($iframe, $width, $height){
		 $iframe = htmlspecialchars_decode($iframe);
		 preg_match("/iframe([\s\S]+?)><\/iframe/", $iframe, $m);
		 $i = explode(" ", $m[1]);
		 foreach($i as $k => $v) {
			 list($n, $c) = explode("=", $v);
			 if($n == "width") {
				 $i[$k] = 'width="' . $width . '"';
			 }
			 if($n == "height") {
				 $i[$k] = 'height="' . $height . '"';
			 }
		 }
		 $attr = join(" ", $i);
		 $attr = str_replace("output=embed&iwloc=B", "output=embed", $attr);
		 $attr = str_replace("output=embed", "output=embed&iwloc=B", $attr);
		 $attr = preg_replace("/;z=\d*/", ";z=14", $attr);
		 echo "<iframe" . $attr . "></iframe>";
	 }
	 
	 public function flash($name, $val = null) { 
		 if(empty($val)) {
			 echo $this->flash[$name];
		 } else {
			 $this->flash[$name] = $val;
		 }
	 }

	 public function flash_success($name) {
		 if(isset($this->flash[$name])) {
			 echo "<div class='alert alert-success'>", $this->flash[$name], "</div>";
		 }
	 }

	 public function flash_error($name) {
		 if(isset($this->flash[$name])) {
			 echo "<div class='alert alert-error'>", $this->flash[$name], "</div>";
		 }
	 }

	 public function flash_info($name) {
		 if(isset($this->flash[$name])) {
			 echo "<div class='alert alert-info'>", $this->flash[$name], "</div>";
		 }
	 }

	 public static function h($data) {
		 if(is_array($data)){
			 foreach($data as $key => $value){
				 $data[$key] = self::h($value);
			 }
			 return $data;
		 }elseif(is_string($data)){
			 return htmlspecialchars($data,ENT_QUOTES);
		 }else{
			 return $data;
		 }
	 }

	 /**
	  * prepare for gettext(soon, maybe?)
	  */
	 public static function _($var) {
		 return $var;
	 }

	 public function number($number) {
	   echo number_format($number ? $number : 0);
	 }

	 private function each($data, $call_or_parts) {
	   $length = count($data) - 1;
	   $tmp_parts = $this->view_parts . $call_or_parts . $this->ext;
	   if(is_file($tmp_parts)) {
		   //ok, it should be a template_parts
		   $template_context = file_get_contents($tmp_parts);
		   foreach($data as $key => $item) {
			   $vars = $this->vars;
			   call_user_func(function() use($item, $vars, $template_context) {
					   $vars["item"] = $item;
					   extract($vars);
					   eval("?>" . $template_context . "<?");
				   });
		  }
	  } elseif(is_callable($call_or_parts)) {
		  //so, it should be a action-caller
		  foreach($data as $key => $item) {
			  call_user_func_array($call, array($key, $item, $key === $length));
		  }
	  }
	}

	 public function compare($val1, $val2, $output = "active") {
		 if($val1 === $val2) {
			 echo $output;
		 }
	 }
	 
	 public function page_active($page) {
		 $this->compare($page, $this->get_page(), 'class="active view_nav_side"');
	 }

	 //bootstrap helper
	 public function tooltip($tooltip, $position = "right") {
		 //use font awesome...
		 echo '<i class="fa fa-question-circle view-tooltip" data-toggle="tooltip" title="' . $tooltip . '" data-placement="' . $position . '"></i>';
	 }

	 //introJS helper
	 public function introbtn() {
		 echo '<small><span class="btn btn-sm" id="intro-control">この画面の使い方</span></small>';
	 }

	 public function intro($step, $message, $bind_top = false) {
		 if($bind_top && $this->get_page() !== "index/index") {
			 return false;
		 }
		 echo "data-step='{$step}' data-intro='{$message}'";
	 }
}

if(!function_exists("h")) {
	function h($data) {
		return view_helper::h($data);
	}
}

if(!function_exists("_")) {
	function _($vars) {
		return view_helper::_($vars);
	}
}