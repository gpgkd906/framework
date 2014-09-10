<?php

class facebook_controller extends application {
	
	public function app1() {
		$app = App::helper("facebook_appengine")->find("approval1");
		$this->assign(get_defined_vars());
	}
	
}