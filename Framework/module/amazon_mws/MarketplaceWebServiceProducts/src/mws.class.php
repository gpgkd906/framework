<?php

class mws {
  private $info = array(
                        "access_key" => "",
                        "secret_access_key" => "",
                        "application_name" => "",
                        "application_version" => "",
                        "merchant_id" => "",
                        "marketplace_id" => "",
                        "service_url" => "https://mws.amazonservices.jp/Products/2011-10-01",
                        "proxy_host" => null,
                        "proxy_port" => -1,
                        "max_error_retry" => 3
                        );
  private $queries = array();
  private $client = null;
  private $request = null;
  private $response = null;

  public function set_access_key($val){
    $this->info["access_key"] = $val;
  }

  public function set_secret_access_key($val){
    $this->info["secret_access_key"] = $val;
  }

  public function set_application_name($val){
    $this->info["application_name"] = $val;
  }

  public function set_application_version($val){
    $this->info["application_version"] = $val;
  }

  public function set_merchant_id($val){
    $this->info["merchant_id"] = $val;
  }

  public function set_marketplace_id($val){
    $this->info["marketplace_id"] = $val;
  }
  
  private function get_client(){
    require "../Client.php";
    $info = $this->info;
    $this->client = new MarketplaceWebServiceProducts_Client(
                                                             $info["access_key"],
                                                             $info["secret_access_key"],
                                                             $info["application_name"],
                                                             $info["application_version"],
                                                             array(
                                                                   "ServiceURL" => $info["service_url"],
                                                                   "ProxyHost" => $info["proxy_host"],
                                                                   "ProxyPort" => $info["proxy_port"],
                                                                   "MaxErrorRetry" => $info["max_error_retry"]
                                                                   )
                                                             );
    return $this->client;
  }

  private function get_request($request){
    $request = $request . "Request";
    $request_file = dirname(__FILE__) . "/../Model/" . $request . ".php";
    $request_name = "MarketplaceWebServiceProducts_Model_" . $request;
    require "../Model.php";
    require $request_file;
    $info = $this->info;
    $this->request = new $request_name();
    $this->request->setSellerId($info["merchant_id"]);
    $this->request->setMarketplaceId($info["marketplace_id"]);
    return $this->request;
  }

  public function get_response() {
    return $this->response;
  }
  
  public function __call($name, $param){
    if(preg_match("/^set/", $name)) {
      $this->queries[$name] = $param;
    } else {
      return $this->call_api($name);
    }
  }

  private function call_api($call){
    $this->get_client();
    $this->get_request($call);
    if(!empty($this->queries)) {
      foreach($this->queries as $method => $param) {
        call_user_func_array(array($this->request, $method), $param);
      }
    }
    try {
      $this->response = call_user_func(array($this->client, $call), $this->request);
      return $this->response;
    } catch(Exception $ex) {
      echo("Caught Exception: " . $ex->getMessage() . "\n");
      echo("Response Status Code: " . $ex->getStatusCode() . "\n");
      echo("Error Code: " . $ex->getErrorCode() . "\n");
      echo("Error Type: " . $ex->getErrorType() . "\n");
      echo("Request ID: " . $ex->getRequestId() . "\n");
      echo("XML: " . $ex->getXML() . "\n");
      echo("ResponseHeaderMetadata: " . $ex->getResponseHeaderMetadata() . "\n");
    }
  }

}