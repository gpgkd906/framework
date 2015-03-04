<?php

class mail_helper extends helper_core {
	private $mailer = null;
		
	public function init() {
		$mailer = $this->get_module("mail");
		$mailer->Username = "check@penseur.co.jp";
		$mailer->Password = "0706";
		$mailer->Host = "mail.penseur.co.jp";
		$mailer->Port = 587;
		$mailer->Helo = "mail.penseur.co.jp";
		$mailer->CharSet = "UTF-8";
		$mailer->Encoding = "8bit";
		$this->mailer = $mailer;
	}

	public function send($data, $template, $to = null) {
		$this->mailer->From = "photo@penseur.co.jp";
		$this->mailer->FromName = "スケジュールシステム";
		$this->mailer->Subject = "スタジオスケジュール予約";
		$this->mailer->assign($data);
		$this->mailer->template($template);
		if(empty($to)) {
			$this->mailer->set_address(array(
					//"kuratate@penseur.co.jp", "photo@penseur.co.jp"
					"check@penseur.co.jp", "kuratate@penseur.co.jp"
											 ));
		} else {
			$this->mailer->set_address($to);
		}
		$this->mailer->Send();
	}
	
	public function contact($data, $user) {
		
	}

}