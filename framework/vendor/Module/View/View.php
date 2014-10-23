<?php 
/**
 *   Licensed under the MIT license:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 *   author: chenhan,gpgkd906@gmail.com
 *   website: http://dev.gpgkd906.com/MyProject/
 */
namespace Module\View;
use Module\View\Chemplate;

class View extends Chemplate {
	private $theme="";
	private $template_file;
	
	public function __construct(){
		parent::__construct();
		$this->_cache_dir='/var/www/dashboard/framework/view/chemplate/cache/';
		$this->_template_dir='/var/www/dashboard/framework/view/chemplate/templates/';
		$this->_template_chen_dir='/var/www/dashboard/framework/view/chemplate/templates_chen/';
	}
	
	public function set_template($template){
		if(!preg_match("/\.tpl$/", $template)) { 
			$this->template_file = $template . ".tpl";
		}
	}
	
	public function response(){
		if(isset($this->template_file)){
			parent::display($this->template_file);
		}
	}
	
}