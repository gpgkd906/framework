<?php

/**
 * api2は公開apiであって、認証が必要としない
 */

class api2_controller extends api {
	public $authorization = false;
	
	public function partners() {
		$status = false;
		if(true) {
			$partners = array("watami");
			$status = true;
		}
		$this->assign(get_defined_vars());
	}
}