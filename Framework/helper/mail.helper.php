<?php

class mail_helper extends helper_core {
	private $mailer = null;
	private $address_pool = array();
	
	public function __construct() {
		$mailer = App::module("mail2");
		$mailer->Username = "";
		$mailer->Password = "";
		$mailer->Host = "";
		$mailer->Port = 587;
		$mailer->Helo = "";
		$mailer->CharSet = "UTF-8";
		$mailer->Encoding = "8bit";
		$this->mailer = $mailer;
	}

	public function send($data, $template, $to = null) {
		$this->mailer->From = "";
		$this->mailer->FromName = "";
		$this->mailer->Subject = isset($data["subject"]) ? $data["subject"] : "";
		$this->mailer->assign($data);
		$this->mailer->template($template);
		if(empty($to)) {
			$this->mailer->set_address(array(""));
		} else {
			$this->mailer->set_address($to);
		}
		$this->mailer->Send();
	}

}
