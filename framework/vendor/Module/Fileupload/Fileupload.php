<?php
namespace Module\Fileupload;

use Module\Fileupload\UploadHandler;

class Fileupload {
	private $options = array();
	private $handler = null;
	
	public function set($name, $value) {
		$options[$name] = $value;
	}
	
	public function get($name) {
		if(isset($options[$name])) {
			return $options[$name];
		}
	}
	
	public function handle() {
		if($this->handler === null) {
			$this->handler = new UploadHandler();
		}
	}
}