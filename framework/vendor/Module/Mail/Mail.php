<?php
/**
 * use phpmailer
 */
require "phpmailer/class.phpmailer.php";

namespace Module\Mail;

class Mail extends PHPMailer{

	public $Host = "";
	public $Port = null;
	public $Helo = "";
	public $SMTPAuth = true;
	public $Username = "";
	public $Password = "";
	public $CharSet = "";
	public $Encoding = "";
	public $template_vars = array();
	
	public function __construct(){
		$this->isSmtp();
		$this->WordWrap=50;
	}
	
	public function assign($data) {
		$this->template_vars = $data;
	}
	
	public function template($template, $html_mode = true) {
		foreach($this->template_vars as $key => $val) {
			if(is_array($val)) {
				$val = join(",", $val);
			}
			$template = str_replace('{' . $key . '}', $val, $template);
		}
		if($html_mode) {
			$template = nl2br($template);
			$this->MsgHTML($template);
		}
		$this->AltBody = $template;
	}
	
	public function set_address($mail) {
		if(is_array($mail)) {
			foreach($mail as $address) {
				$this->AddAddress($address);
			}
		} else {
			$this->AddAddress($mail);
		}
	}
	
}