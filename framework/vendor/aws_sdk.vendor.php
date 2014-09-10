<?php

require dirname(__FILE__) . "/aws/vendor/autoload.php";
use Aws\Common\Aws;
use Aws\Common\Enum\Region;
use Aws\Sns\SnsClient;

class aws_sdk_vendor extends helper_core {

	private $aws_core = null;
	private $apps = array();
	private $access = "";
	private $secret = "";
	
	public function __construct() {
		$this->aws_core = Aws::factory(array(
				"key" => $this->access,
				"secret" => $this->secret,
				"region" => Region::TOKYO
											 ));
	}
	
	public function sns_init() {
		$sns = $this->aws_core->get("sns");
		return $this->apps["sns"] = $sns;
	}
	
	public function sns_create_endpoint($device_id, $device_type) {
		if(empty($this->apps["sns"])) {
			$this->sns_init();
		}
		switch($device_type) {
			case "APNS": 
				$arn = "arn:aws:sns:ap-northeast-1:{apns-code}";
				break;
			case "GCM":
				$arn = "arn:aws:sns:ap-northeast-1:{gcm-code}";
				break;
		}
		try {
			$result = $this->apps["sns"]->createPlatformEndpoint(array(
					"PlatformApplicationArn" => $arn,
					"Token" => $device_id,
					"CustomUserData" => (string) $device_id,
																	   ));
		} catch(Exception $e) {
			//why do we have this?ok, it's web;
		}
		return $result["EndpointArn"];
	}
	
	public function sns_publish($device_id, $device_type, $endpointArn, $message) {
		if(empty($this->apps["sns"])) {
			$this->sns_init();
		}
		switch($device_type) {
			case "APNS":
				$this->apps["sns"]->publish(array(
						'MessageStructure' => 'json',
						'Message' => json_encode(array(
								'default' => $message,
								'APNS_SANDBOX' => json_encode(
									array(
										'aps' => array(
											'alert' => $message,
											'badge' => 0,
											'sound' => 'default'
													   )
										  ))
													   )),
						"TargetArn" => $endpointArn
												  ));
				break;
			case "GCM": 
				//AmazonSNSからタイムラグが発生
				//curlで直接GCM叩いて送信
				//追記：直接GCM叩いてもタイムラグが発生、T.T、Googleさんガンバレ
				$send_content = array(
					"name"=> "私の親孝行",
					"title"=> "私の親孝行",
					"message"=> $message
									  );
				$gcm_api_key = "";
				$url = "https://android.googleapis.com/gcm/send";
				$fields = array(
					"collapse_key" => "score_update",
					"delay_while_idle" => true,
					"registration_ids" => array($device_id),
					"data" => $send_content					
								);
				$headers = array(
					"Authorization: key=" . $gcm_api_key,
					"Content-Type: application/json"
								 );
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
				$result = curl_exec($ch);
				if($result === FALSE){
					return false;
				}
				curl_close($ch);
				break;
		}
	}
}