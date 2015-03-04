<?php
require('UploadHandler.php');

class fileupload {
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