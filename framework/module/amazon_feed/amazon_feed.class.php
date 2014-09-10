<?php

class amazon_feed {
  private $info = array(
                        "access_key" => "",
                        "secret_access_key" => "",
                        "application_name" => "",
                        "application_version" => "",
                        "merchant_id" => "",
                        "marketplace_id" => "",
                        "service_url" => "https://mws.amazonservices.jp",
                        "proxy_host" => null,
                        "proxy_port" => -1,
                        "max_error_retry" => 3
                        );
  private $queries = array();
  private $client = null;
  private $request = null;
  private $response = null;
  private $lib_path = null;
  
  public function __construct() {
    set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__));
    $this->lib_path = dirname(__FILE__) . "/MarketplaceWebService";
  }
  
  public function set_lib_path($lib_path) {
    $this->lib_path = $lib_path;
  }
  
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
  
  public function set_service_url($val){
    $this->info["service_url"] = $val;
  }
  
  public function set_proxy_host($val){
    $this->info["proxy_host"] = $val;
  }
  
  public function set_proxy_port($val){
    $this->info["proxy_port"] = $val;
  }
  
  public function set_max_error_retry($val){
    $this->info["max_error_retry"] = $val;
  }
  
  public function use_helper($helper){
    $helper_name = "MarketplaceWebService_Model_" . $helper;
    if(!class_exists($helper_name)) {
      require $this->lib_path . "/Model/" . $helper . ".php";
    }
    $helper = new $helper_name();
    return $helper;
  }
  
  public function use_client(){
    if(!class_exists("MarketplaceWebService_Client")) {
      require $this->lib_path . "/Client.php";
    }
    $info = $this->info;
    $this->client = new MarketplaceWebService_Client(
                                                             $info["access_key"],
                                                             $info["secret_access_key"],
                                                             array(
                                                                   "ServiceURL" => $info["service_url"],
                                                                   "ProxyHost" => $info["proxy_host"],
                                                                   "ProxyPort" => $info["proxy_port"],
                                                                   "MaxErrorRetry" => $info["max_error_retry"]
                                                                   ),
                                                             $info["application_name"],
                                                             $info["application_version"]
                                                             );
    return $this->client;
  }
  
  private function get_request($request){
    $request = $request . "Request";
    $request_file = $this->lib_path . "/Model/" . $request . ".php";
    $request_name = "MarketplaceWebService_Model_" . $request;
    if(!class_exists("MarketplaceWebService_Model")) {
      require $this->lib_path . "/Model.php";
    }
    if(!class_exists($request_name)) {
      require $request_file;
    }
    $info = $this->info;
    $this->request = new $request_name();
    $this->request->setSellerId($info["merchant_id"]);
    $this->request->setMarketplaceId($info["marketplace_id"]);
    return $this->request;
  }
  
  public function use_mock(){
    if(!class_exists("MarketplaceWebService_Mock")) {
      require $this->lib_path . "/Mock.php";
    }
    $this->client = new MarketplaceWebService_Mock();
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
    if(!$this->client) {
      $this->use_client();
    }
    $this->get_request($call);
    if(!empty($this->queries)) {
      foreach($this->queries as $method => $param) {
        call_user_func_array(array($this->request, $method), $param);
      }
    }
    $this->queries = array();
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

  public function submit_delete($item) {
    $feed = array();
    $feed[] = join("\t", array(
			       "TemplateType=Office", "Version=2013.1106"
  			       ));
    $feed[] = join("\t", array(
			       "商品管理番号", "商品コード(JANコード等)", "商品コードのタイプ", "商品名", "ブランド名", "メーカー名", "メーカー型番", "商品タイプ", "商品説明文", "アップデート・削除", "通貨コード", "商品の販売価格", "在庫数", "リードタイム(出荷までにかかる作業日数)", "商品のコンディション", "商品のコンディション", "商品の公開日", "予約商品の販売開始日", "商品の入荷予定日", "メーカー製造中止", "メーカー希望小売価格", "使用しない支払い方法", "配送日時指定SKUリスト", "セール価格", "セール開始日", "セール終了日", "最大同梱可能数", "ギフトメッセージ", "ギフト包装", "最大注文個数", "パッケージ商品数", "商品の入り数", "商品コードなしの理由", "配送重量の単位", "配送重量", "商品の重量", "1ユニット重量の単位", "1ユニットの内容量", "1ユニットの内容量の単位", "1ユニットの長さ", "1ユニットの長さの単位", "商品の重量", "商品の重量の単位", "商品の長さの単位", "商品の長さ", "商品の幅", "商品の高さ", "商品説明の箇条書き1", "商品説明の箇条書き2", "商品説明の箇条書き3", "商品説明の箇条書き4", "商品説明の箇条書き5", "検索キーワード1", "検索キーワード2", "検索キーワード3", "検索キーワード4", "検索キーワード5", "出品者カタログ番号", "推奨ブラウズノード1", "推奨ブラウズノード2", "プラチナキーワード1", "プラチナキーワード2", "プラチナキーワード3", "プラチナキーワード4", "プラチナキーワード5", "商品メイン画像URL", "商品のサブ画像URL1", "商品のサブ画像URL2", "商品のサブ画像URL3", "商品のサブ画像URL4", "商品のサブ画像URL5", "商品のサブ画像URL6", "商品のサブ画像URL7", "商品のサブ画像URL8", "フルフィルメントセンターID", "商品パッケージの長さ", "商品パッケージの幅", "商品パッケージの高さ", "商品パッケージの長さの単位", "商品パッケージの重量", "商品パッケージの重量の単位", "親子関係の指定", "親商品のSKU(商品管理番号)", "親子関係のタイプ", "バリエーションテーマ", "カラー", "カラーマップ", "サイズ", "インクカラー1", "インクカラー2", "ペンの線幅", "線幅の単位", "再生材使用率", "素材タイプ1", "素材タイプ2", "ペン先の説明1", "ペン先の説明2", "芯の硬さ", "線幅タイプ", "商品の直径", "商品の直径の単位", "配送日時指定SKUリスト", "最大拡張サイズ", "最大拡張サイズの単位", "商品の内径", "商品の内径の単位", "つづりひものとじ枚数", "つづりひもの最大とじ厚", "つづりひもの最大とじ厚の単位", "商品の穴数", "カバー素材", "印刷済み", "コーティングの説明", "紙の種類", "用紙のサイズ", "紙の白色度", "罫線タイプ"
  			       ));
    $feed[] = join("\t", array(
			       "item_sku", "external_product_id", "external_product_id_type", "item_name", "brand_name", "manufacturer", "part_number", "feed_product_type", "product_description", "update_delete", "currency", "standard_price", "quantity", "fulfillment_latency", "condition_type", "condition_note", "product_site_launch_date", "merchant_release_date", "restock_date", "is_discontinued_by_manufacturer", "list_price", "optional_payment_type_exclusion", "delivery_schedule_group_id", "sale_price", "sale_from_date", "sale_end_date", "max_aggregate_ship_quantity", "offering_can_be_gift_messaged", "offering_can_be_giftwrapped", "max_order_quantity", "item_package_quantity", "number_of_items", "missing_keyset_reason", "website_shipping_weight_unit_of_measure", "website_shipping_weight", "item_display_weight", "item_display_weight_unit_of_measure", "item_display_volume", "item_display_volume_unit_of_measure", "item_display_length", "item_display_length_unit_of_measure", "item_weight", "item_weight_unit_of_measure", "item_length_unit_of_measure", "item_length", "item_width", "item_height", "bullet_point1", "bullet_point2", "bullet_point3", "bullet_point4", "bullet_point5", "generic_keywords1", "generic_keywords2", "generic_keywords3", "generic_keywords4", "generic_keywords5", "catalog_number", "recommended_browse_nodes1", "recommended_browse_nodes2", "platinum_keywords1", "platinum_keywords2", "platinum_keywords3", "platinum_keywords4", "platinum_keywords5", "main_image_url", "other_image_url1", "other_image_url2", "other_image_url3", "other_image_url4", "other_image_url5", "other_image_url6", "other_image_url7", "other_image_url8", "fulfillment_center_id", "package_length", "package_width", "package_height", "package_length_unit_of_measure", "package_weight", "package_weight_unit_of_measure", "parent_child", "parent_sku", "relationship_type", "variation_theme", "color_name", "color_map", "size_name", "ink_color1", "ink_color2", "line_size", "line_size_unit_of_measure", "recycled_content_percentage", "material_type1", "material_type2", "tip_description1", "tip_description2", "item_hardness", "point_type", "item_diameter_derived", "item_diameter_unit_of_measure", "delivery_schedule_group_id", "maximum_size", "maximum_size_unit_of_measure", "core_size", "core_size_unit_of_measure", "number_of_fasteners", "fastener_capacity", "fastener_capacity_unit_of_measure", "hole_count", "cover_material_type", "pre_printed_text", "coating_description", "paper_finish", "paper_size", "brightness", "ruling_type"
            		       ));
    $feed[] = join("\t", array(
			       $item->sku, $item->jancode, "EAN", 
  			       ));
    return $this->submit($feed);    
  }

  public function submit_create($item){
    $feed = array();
    $feed[] = join("\t", array(
  			       "TemplateType=Offer", "Version=2012.1213"
  			       ));
    $feed[] = join("\t", array(
  			       "商品管理番号", "販売価格", "在庫数", "商品コード(JANコード等)", "商品コードのタイプ", "商品のコンディション", "商品のコンディション説明", "対象ASIN", "商品名", "動作タイプ", "セール価格", "セール開始日", "セール終了日", "リードタイム(出荷までにかかる作業日数)", "商品の公開日", "ギフト包装", "ギフトメッセージ", "フルフィルメントセンターID", "使用しない支払い方法", "出品者SKUのメイン画像URL", "出品者SKUのサブ画像URL1 ", "出品者SKUのサブ画像URL2", "出品者SKUのサブ画像URL3", "出品者SKUのサブ画像URL4", "出品者SKUのサブ画像URL5"
  			       ));
    $feed[] = join("\t", array(
  			       "sku", "price", "quantity", "product-id", "product-id-type", "condition-type", "condition-note", "ASIN-hint", "title", "operation-type", "sale-price", "sale-start-date", "sale-end-date", "leadtime-to-ship", "launch-date", "is-giftwrap-available", "is-gift-message-available", "fulfillment-center-id", "optional-payment-type-exclusion", "main-offer-image", "offer-image1", "offer-image2", "offer-image3", "offer-image4", "offer-image5"
            		       ));
    $feed[] = join("\t", array(
  			       $item->sku, $item->origin_price, $item->quantity, $item->jancode, "EAN", "New"
  			       ));
    return $this->submit($feed);
  }

  public function submit($feed){
    $feed = join(PHP_EOL, $feed);
    if(!$this->client) {
      $this->use_client();
    }
    $request = "SubmitFeedRequest";
    $request_file = $this->lib_path . "/Model/" . $request . ".php";
    $request_name = "MarketplaceWebService_Model_" . $request;
    if(!class_exists("MarketplaceWebService_Model")) {
      require $this->lib_path . "/Model.php";
    }
    if(!class_exists("MarketplaceWebService_Model_IdList")) {
      require $this->lib_path . "/Model/IdList.php";
    }
    if(!class_exists($request_name)) {
      require $request_file;
    }
    $feedHandle = @fopen('php://temp', 'rw+');
    fwrite($feedHandle, $feed);
    rewind($feedHandle);
    $info = $this->info;
    $this->request = new $request_name();
    $this->request->setMerchant($info["merchant_id"]);

    $this->request->setMarketplaceIdList(array("Id" => array($info["marketplace_id"])));
    $this->request->setFeedType("_POST_FLAT_FILE_LISTINGS_DATA_");
    $this->request->setContentMd5(base64_encode(md5(stream_get_contents($feedHandle), true)));
    rewind($feedHandle);
    $this->request->setPurgeAndReplace(false);
    $this->request->setFeedContent($feedHandle);
    rewind($feedHandle);
    try {
      $this->response = $this->client->submitFeed($this->request);
      return $this->response;
    } catch(Exception $ex) {
      echo("Caught Exception: " . $ex->getMessage() . "<br/>");
      echo("Response Status Code: " . $ex->getStatusCode() . "<br/>");
      echo("Error Code: " . $ex->getErrorCode() . "<br/>");
      echo("Error Type: " . $ex->getErrorType() . "<br/>");
      echo("Request ID: " . $ex->getRequestId() . "<br/>");
      echo("XML: " . $ex->getXML() . "</br>");
      echo("ResponseHeaderMetadata: " . $ex->getResponseHeaderMetadata() . "<br/>");
    }
  }

}