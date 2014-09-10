<?php

class form_helper extends helper_core {
	private $core = null;
	private $contact = null;
	
	public function init($controller) {
		$this->core = $controller->get_module("form");
	}

	public function contact($customize = null) {
		$contact = $this->core->create("contact");
		$contact->addTextarea("detail")->setClass("detail", "form-control")->setRows("detail", 3);
		$contact->setClass("submit", "btn btn-default");
		$contact->setClass("reset", "btn btn-default");
		$contact->addCheckRule("detail", checker::Exists);
		$this->contact = $contact;
		if(isset($customize) && is_callable($customize)) {
			call_user_func($customize, $this->contact);
		}
		return $this->contact;
	}

	public function contact_handler($complete_handler) {
		$this->contact->submit();
		$this->contact->confirm(null, function($contact) use($complete_handler) {
				call_user_func($complete_handler, $contact->getData());
			});
	}
	

}