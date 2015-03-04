<?php
/**
 *
 *   Licensed under the MIT license:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 *   author: chenhan,gpgkd906@gmail.com
 *   website: http://dev.gpgkd906.com/MyProject/
 */
class simple_check {
  //通常，これらの画像タイプで対応できる，稀に他の画像タイプを対応さしたい場合,メソッドaddFormatで追加しよう。
  protected static $format=array("jepg","png","gif","bmp");
  protected static $error=array();

  public static function addFormat($format){
    self::$format=array_merge(self::$format,$format);
  }

  public static function isImg($file,$flag){
    $info=getimagesize($file);
    $result = preg_match("/^image\/(".join("|",self::$format).")$/",$info["mime"]);
    if(!$result){
      self::$error[$flag] = "該当ファイルは画像ファイルではありません，確認してからアップロードしてください";
    }
    return $result;
  }
  
  public static function isUpload($file,$flag){
    $result = is_uploaded_file($file);
    if(!$result){
      self::$error[$flag] = "アップロードされたファイルではありません，システム管理者と連絡してください";
    }
    return $result;
  }

  public static function isNull($val,$flag){
    $result = empty($val)||!isset($val);
    if($result){
      self::$error[$flag] = "この項目は必ず入力してください";
    }
    return !$result;
  }

  public static function isNumber($val,$flag,$null=true){
    $result = (bool)(is_numeric($val));
    if($null && empty($val)){
      $result=true;
    }
    if(!$result){
      self::$error[$flag] = "数字ではありません，数字を入力してください";
    }
    return $result;
  }
  
  public static function isJText($val,$flag){
    $result = (bool)preg_match("/^[あ-んァ-ヾ一-龠\w\s,、，。@\-\/\_\<\>]*$/",$val);
    if(!$result){
      self::$error[$flag] = "日本語や英数字を入力してください";
    }
    return $result;
  }

  public static function escape($data) {
    if(is_array($data)){
      foreach($data as $key => $value){
        $data[$key]=self::escape($value);
      }
      return $data;
    }elseif(is_string($data)){
      return htmlspecialchars($data,ENT_QUOTES);
    }else{
      return $data;
    }
  }

  public static function isError(){
    return (bool)count(self::$error);
  }

  public static function hasError($key){
    return isset(self::$error[$key]);
  }

  public static function forceError($key,$error){
    self::$error[$key]=$error;
  }

  public static function error($key,$mode="display"){
    if($mode=="fetch"){
      return self::$error[$key];
    }
    echo self::$error[$key]?self::$error[$key]:"";
  }

  public static function unsetError($key){
    unset(self::$error[$key]);
  }

  public static function debug(){
    var_dump(self::$error);
  }

}
