<?php

/**
 * consoleコントローラはcliモードでなければ動きません
 */

class console_controller extends application {
	
	public function before_action() {
		if(php_sapi_name() !=="cli") {
			$this->route->forbidden();
		}
		error_handler::off();
	}
	
	public function alerms() {
		App::model("alerms")->auto_publish();
	}

	public function alerms_mail() {
		App::model("alerms")->auto_mail();
	}

	public function push() {
		App::model("alerms")->find("account_id", 4);
		App::model("alerms")->auto_publish();
	}

	public function mail() {
		App::model("alerms")->find("account_id", 4);
		App::model("alerms")->auto_mail();
	}

	public function update_specify_alerms() {
		App::model("alerms")->update_specify_date();
	}
	
}    
    