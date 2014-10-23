<?php
/**
 * phpmailerから継承したmailモジュールは簡単にメール送信することを目的に対して
 * このmail2モジュールは素早く大量メール送信を目的としています。
 * もちろんphpmailerも同じメールを複数のアドレスに送信することが可能だが
 * mail2モジュールは本文が異なるの複数メールを高速送信と目指しています。
 */
namespace Module\Mail2;

class Mail2 {
	
	public $Host = "";
	public $Port = 587;
	public $Helo = "";
	//SMTPの使用は必須になるので不要
	//private $SMTPAuth = true;
	public $From = "";
	public $FromName = "";
	public $Username = "";
	public $Password = "";
	public $CharSet = "UTF-8";
	public $Encoding = "base64";
	public $Timeout = 10;
	public $LE = "\n";
	//
	public $template_vars = array();
	//メールを発送するまで何通溜めとくの設定
	public $max_store = 100;
	private $mail_pool = array();
	//
	public $Body = null;
	public $Address = array();
	public $Subject = "";

	public function __construct() {
		if(!function_exists("curl_multi_init")) {
			throw new Exception("[mail2] : curlライブラリが必要です。");
		}
	}

	public function __destruct() {
		$this->flush();
	}

	public function assign($data) {
		$this->template_vars = $data;
	}
	
	/**
	 * sendまで複数次呼び出すことで、テンプレートも複数回連結できる
	 */
	public function template($template) {
		foreach($this->template_vars as $key => $val) {
			if(is_array($val)) {
				$val = join(",", $val);
			}
			$template = str_replace('{' . $key . '}', $val, $template);
		}
		if($this->Body !== null) {
			$template = PHP_EOL . $template;
		}
		$template = nl2br($template);
		$this->Body .= $template;
	}

	public function set_address($mail) {
		if(!is_array($mail)) {
			$mail = array($mail);
		}
		foreach($mail as $address) {
			$this->Address[] = $address;
		}
	}
	
    /**
	 *メソッド名がsendでありながら、実際に送信処理を行わない
	 *「現在設定されているメールデータ」をパッケージするだけ
	 * 設定した累積値を越える場合だけ、flushを呼び出して、実際のメール送信を行わせる。
	 * sendに設定したのはユーザーに単純なインタフェースを提供するためです。
	 * 実際flushはユーザーからも呼び出せるのですが、普通に触らなくても勝手に動くはず。
	 */
	public function send($after_routine = null) {
		//config current mail, append to mail pool, clear current mail;
		foreach($this->Address as $address) {
			$header = $this->build_header($address);
			$content = $this->build_content($address, $header);
			$this->mail_pool[] = array(
				"content" => $content,
				"after_routine" => $after_routine
			);
			if(count($this->mail_pool) > $this->max_store) {
				$this->flush();
			}
		}
		//reset user data
		$this->Address = array();
		$this->Body = null;
		$this->Subject = "";
	}
	
	//貯めているメールを送信する
	public function flush() {
		if(empty($this->mail_pool)) {
			return false;
		}
		$multi_handler = curl_multi_init();
		$chs = array();
		foreach($this->mail_pool as $key => $package) {
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $this->Host);
			curl_setopt($curl, CURLOPT_PORT, $this->Port);
			curl_setopt($curl, CURLOPT_TIMEOUT, $this->Timeout);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $package["content"]);
			curl_multi_add_handle($multi_handler, $curl);
			$chs[$key] = $curl;
		}
		$running = null;
		do {
			curl_multi_exec($multi_handler, $running);
		} while($running);
		foreach($chs as $key => $curl) {
			$info = curl_getinfo($curl);
			$raw = curl_multi_getcontent($curl);
			if(is_callable($this->mail_pool[$key]["after_routine"])) {
				call_user_func($this->mail_pool[$key]["after_routine"], $raw, $info);
			}
			curl_multi_remove_handle($multi_handler, $curl);
			curl_close($curl);
		}
		curl_multi_close($multi_handler);
		//clear the mail pool, do not forgot it
		$this->mail_pool = array();
	}

	private function format_subject(){
		return $this->Subject ? "=?{$this->CharSet}?B?" . base64_encode($this->SecureHeader($this->Subject)) . '?= ' : '';
	}

	private function build_header($to) {
		return join(PHP_EOL, array(
				'Return-path: <' . $this->From . '>',
				'Date: ' . date('r'),
				"From: {$this->FromName}<" . $this->From . '>',
				'MIME-Version: 1.0',
				'Subject: ' . $this->format_subject(),
				'To: ' . $to,
				"Content-Type: text/html; charset={$this->CharSet}; format=flowed",
				"Content-Transfer-Encoding: {$this->Encoding}"
		));
	}

	private function build_content($to, $header) {
		return join(PHP_EOL, array(
				"EHLO " . $this->Helo,
				"AUTH LOGIN",
				base64_encode($this->Username),
				base64_encode($this->Password),
				"MAIL FROM:" . $this->From,
				"RCPT TO:" . $to,
				"DATA",
				$header,
				$this->EncodeString($this->Body, $this->Encoding),
				".",
				"QUIT"
		));
	}

	//↓↓↓methods from phpmailer ↓↓↓
	/**
	 * Encodes string to requested format.
	 * Returns an empty string on failure.
	 * @param string $str The text to encode
	 * @param string $encoding The encoding to use; one of 'base64', '7bit', '8bit', 'binary'
	 * @access public
	 * @return string
	 */
	public function EncodeString ($str, $encoding = 'base64') {
		$encoded = '';
		switch(strtolower($encoding)) {
			case 'base64':
				$encoded = chunk_split(base64_encode($str), 76, $this->LE);
				break;
			case '7bit':
			case '8bit':
				$encoded = $this->FixEOL($str);
				//Make sure it ends with a line break
				if (substr($encoded, -(strlen($this->LE))) != $this->LE)
					$encoded .= $this->LE;
				break;
			case 'binary':
				$encoded = $str;
				break;
		}
		return $encoded;
	}

	/**
	 * Changes every end of line from CR or LF to CRLF.
	 * @access private
	 * @return string
	 */
	private function FixEOL($str) {
		$str = str_replace("\r\n", "\n", $str);
		$str = str_replace("\r", "\n", $str);
		$str = str_replace("\n", $this->LE, $str);
		return $str;
	}

	/**
	 * Strips newlines to prevent header injection.
	 * @access public
	 * @param string $str String
	 * @return string
	 */
	public function SecureHeader($str) {
		$str = str_replace("\r", '', $str);
		$str = str_replace("\n", '', $str);
		return trim($str);
	}

	
}