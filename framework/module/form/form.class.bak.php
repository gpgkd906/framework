<?php
/**
 *
 *   Licensed under the MIT license:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 *   author: chenhan,gpgkd906@gmail.com
 *   website: http://dev.gpgkd906.com/MyProject/
 */
class form extends ArrayIterator {
  private $storage=array();
  private $lastId=null;
  private $id="myform";
  private $count=0;
  
  public function __construct(){
    if(!class_exists("Checker")){
      controller::getSingletonInstance()->import("Checker");
    }
  }
  
  /**
   * 明示的にフォームを作らなければいけない
   */
  public function create($id=null){
    if(empty($id)){
      $id=$this->id."_".(++$this->count);
    }
    if(!empty($this->storage[$id])){
      trigger_error("FormHelper:requested form_id was used,old form should be overwrite",E_USER_NOTICE);
    }
    $this->lastId=$id;
    $this->storage[$id] = new form_obj(array("id"=>$id));
    return $this->storage[$id];
  }

  /**
   * idがない場合は最後に生成したフォームを返すように
   */
  public function find($id=null){
    if(empty($id)){
      $id=$this->lastId;
    }
    if(empty($this->storage[$id])){
      trigger_error("FormHelper:undefined Form",E_USER_NOTICE);
      return null;
    }
    return $this->storage[$id];
  }
  
  /**
   * 配列アクセスをできるように
   */
  public function offsetGet($id){
    return $this->find($id);
  }

  /**
   * フォームのクーロンを生成して返す。
   * 同じ構造のフォームを便利に生成する。
   */
  public function copy($from,$to){
    if(empty($this->storage[$from])){
      throw new Exception("FormHelper:Can't copy invalid form.");
    }
    if(isset($this->storage[$to])){
      throw new Exception("FormHelper:Can't overwrite valid form.");
    }
    $new = clone $this->storage[$from];
    $new->setDefault("form_id",$to);
    $new->setFormId($to);
    $this->storage[$to]=$new;
    return $this->storage[$to];
  }
  
}

class form_obj extends ArrayIterator {
  private $id;
  private $count=0;
  private $queue=array();
  private $rule=array();
  private $error=array();
  private $checked=false;
   private $isSubmit=null;
   private $attrs=array(
			"id"=>"",
			"method"=>" method='POST' ",
			"action"=>" action='' ",
			"enctype"=>"",
			);
   private $method="POST";
   private $data=array();
   private $parts=array();
   private $partsObject=array();
   private $autoConfirm=true;
   private $typeCache=array();
   private $confirmed=false;

   public function __construct($arr){
     $id=$arr["id"];
     $this->id=$id;
     $this->addHidden("form_id",$id);
     $this->addHidden("form_mode","edit");
     $this->addButton("submit","確認する")->setType("submit","submit")->setClass("submit","btn btn-success");
     $this->addButton("reset","リセット")->setType("reset","reset")->setClass("reset","btn-danger");
     $this->attrs["id"]="id=".self::_q($id);
     $data=$this->getData();
     if(isset($data["reset"]) && !isset($data["submit"])){
       $this->addError("reset",null);
     }
   }

   public function offsetGet($flag){
     switch($flag){
     case "method":
       return $this->attrs["method"];
       break;
     case "data":
       return $this->data;
       break;
     case "error":
       return $this->error;
       break;
     case "attrs":
       return $this->attrs;
       break;
     case "attrString":
       return join("",$this->attrs);
       break;
     case "helper":
       if(!isset($this->partsObject["hidden"])){
	 $this->partsObject["hidden"]=new form_parts(
						     array(
							   "type"=>"hidden",
							   "parts"=>$this->parts["hidden"]
							   )
						     );
       }
       if(!isset($this->partsObject["button"])){
	 $this->partsObject["button"]=new form_parts(
						     array(
							   "type"=>"button",
							   "parts"=>$this->parts["button"]
							   )
						     );
       }
       $items=array();
       $items[]=$this->partsObject["hidden"]["form_id"];
       $items[]=$this->partsObject["hidden"]["form_mode"];
       $items[]=$this->partsObject["button"]["submit"];
       $items[]=$this->partsObject["button"]["reset"];
       return join("",$items);
       break;
     case "checkboxs":
     case "radios":
       $_flag=str_replace("s","",$flag);
       $multi=true;
       if(isset($this->parts[$_flag])){
	 if(!isset($this->partsObject[$flag])){
	   $this->partsObject[$flag]=new form_parts(
						    array(
							  "type"=>$_flag,
							  "parts"=>$this->parts[$_flag]
							  ),
						    $multi
						    );
	 }
	 return $this->partsObject[$flag];
       } 
      break;
     default:
       if(isset($this->parts[$flag])){
	 if(!isset($this->partsObject[$flag])){
	   $this->partsObject[$flag]=new form_parts(
						    array(
							  "type"=>$flag,
							  "parts"=>$this->parts[$flag]
							  )
						    );
	 }
	 return $this->partsObject[$flag];
       }
       break;
     }
   }

   public function isSubmitted(){
     if($this->isSubmit){
       return $this->isSubmit;
     }
     switch($this->method){
     case "get":
     case "GET":
       if(isset($_GET["form_id"]) && $_GET["form_id"]==$this->id)
	 {
	   $isSubmit=true;
	 }
     break;
     case "post":
     case "POST":
     default:
       if(isset($_POST["form_id"]) && $_POST["form_id"]==$this->id)
	 {
	   $isSubmit=true;
	 }
     break;
     }
     return $this->isSubmit=$isSubmit;
   }

   public function submit($onSubmit=null){
     if($this->isSubmitted()){
       if(!$this->checked){
	 $this->autoValidate();
       }
       $data=$this->getData();
       if(is_callable($onSubmit)){
	 call_user_func_array($onSubmit,array($this));
       }
     }
  }

  public function autoValidate(){
    $queue=array_unique($this->queue);
    foreach($this->rule as $name=>$sets){
      if(!in_array($name,$queue)){
	continue;
      }
      foreach($sets as $set){
	$result=Checker::myFormCheck($this->data[$name],$set);
	if($result["status"]=="error"){
	  $this->error[$name]="<span class='myform_error'>".$result["message"]."</span>";
	}
      }
    }
    $this->checked=true;
  }
  
  public function addError($name,$error){
    $this->error[$name]="<span class='myform_error'>".$error."</span>";
    return $this;
  }

  public function errorCount(){
    return count($this->error);
  }

  public function disableAutoConfirm(){
    $this->autoConfirm=false;
  }

  public function genConfirm($callback){
    if($this->genConfirmed){
      return false;
    }
    //表示上に非必要の要素は排除するまで
    $queue=array_diff(array_unique($this->queue),array("form_id","form_mode"));
    //submit helperを調整
    $this->setValue("submit","送信する");
    $this->setType("reset","submit");
    $this->setValue("reset","戻る");
    $this->setValue("form_mode","confirm");
    foreach($queue as $name){
	$_type=$this->_findTypeByName($name);
	if(empty($_type)){
	  continue;
	}
	$this->setType($name,"hidden");
	if(strpos($name,"[]")!==false){
	  $name=str_replace("[]","",$name);
	}
	switch($_type){
	case "select":
	  $target=$this->parts[$_type][$name];
	  $this->parts[$_type][$name]["_call"]=array("self","getInput");
	  $this->parts[$_type][$name]["_param"][0]["value"]=$this->data[$name];
	  foreach((array)$target["_param"][0]["value"] as $key=>$value){
	    if($value==$this->data[$name]){
	      $this->setLabel($_type,$name,$target["_param"][0]["option"][$key]);
	    }
	  }
	  break;
	case "textarea":
	  $this->parts[$_type][$name]["_call"]=array("self","getInput");
	  $this->setLabel($_type,$name,$this->data[$name]);
	  break;
	case "checkbox":
	case "radio":
	  $data=(array)$this->data[$name];
	foreach($this->parts as $parts){
	  if(in_array($name,array_keys($parts))){
	    $target=$parts[$name];
	    foreach($target as $key=>$value){
	      if(!in_array($value["_param"][0]["value"],$data)){
		$this->parts[$_type][$name][$key]["_param"][0]["type"]="invalid";
	      }
	    }
	  } 
	}
	break;
	case 'file':
	  //do nothing
	  break;
	case "password":
	  $this->parts[$_type][$name]["_call"]=array("self","getInput");
	  $this->setLabel($_type,$name,preg_replace("/./","*",$this->data[$name]));	  
	  break;
	default:
	  $this->parts[$_type][$name]["_call"]=array("self","getInput");
	  $this->setLabel($_type,$name,$this->data[$name]);
	  break;
	}//switch
    }//foreach
    if(is_callable($callback)){
      call_user_func_array($callback,array($this));
    }
    return $this->genConfirmed=true;
  }

  private function defaultConfirmHandler(){
    //do nothing
  }

  public function skipValidate(){
    $this->checked=true;
  }

  public function confirm($onConfirm=null,$onComplete=null){
    if($this->isSubmitted()){
      if(!$this->checked){
	$this->autoValidate();
      }
      $result=count($this->error)>0;
      $data=$this->getData();
      if(!$result){
	if($data["form_mode"]==="confirm" && is_callable($onComplete)){
	  return call_user_func_array($onComplete,array($this));
	}
	if($this->autoConfirm){
	  $this->genConfirm($onConfirm);
	}
      }
      return $result;
    }
  }

  public function getData(){
    if(empty($this->data)){
      switch(strtolower($this->method)){
      case "get":
	$this->data=$_GET;
	break;
      case "post":
      default:
	$this->data=$_POST;
	break;
      }
      //このフォームのデータですか？
      if($this->data["form_id"]!==$this->getFormId()){
	$this->data=array();
      }else{
	//did we got the uploaded file?
	foreach($_FILES as $name=>$file){
	  if(isset($file["tmp_name"][0])){
	    $this->data[$name]=$file;
	  }
	}
	unset($this->data["form_id"]);
	unset($this->data["form_mode"]);
	unset($this->data["submit"]);
	unset($this->data["reset"]);
      }
    }
    return $this->data;
  }

  public function setMethod($method){
    $this->attrs["method"]=" method=".self::_q($method)." ";
    $this->method=$method;
    $this->getData();
    return $this;
  }

  public function setAction($action){
    $this->attrs["action"]=" action=".self::_q($action)." ";
    return $this;
  }

  private function addCheckQueue($name){
    $this->queue[]=$name;
  }

  public function addCheckRule($name,$rule,$errorMessage=null){
    if(is_array($rule)){
      foreach($rule as $key=>$_r){
	$this->rule[$name][]=array(
				   "rule"=>$_r,
				   "message"=>$errorMessage[$key]
				   );
      }
    }else{
      $this->rule[$name][]=array(
				 "rule"=>$rule,
				 "message"=>$errorMessage
				 );
    }
    return $this;
  }

  public function setFormId($id){
    $this->attrs['id']=" id=".self::_q($id)." ";
    $this->id=$id;
  }

  public function getFormId(){
    return $this->id;
  }
  
  public function setFormMode($mode){
    $this->parts["hidden"]["form_mode"]["_param"][0]["value"]=$mode;
  }

  public function getFormMode(){
    return $this->parts["hidden"]["form_mode"]["_param"][0]["value"];
  }

  private function hasParts($name,$type){
    if(empty($this->parts[$type][$name])){
      return false;
    }
    return true;
  }

  public function setInputAttr($type,$name,$attr,$value){
    if($this->hasParts($name,$type)){
      if(in_array($type,array("checkbox","radio"))){
	foreach($this->parts[$type][$name] as $key=>$obj){
	  $this->parts[$type][$name][$key]["_param"][0][$attr]=$value;
	}
      }else{
	$this->parts[$type][$name]["_param"][0][$attr]=$value;
      }
    }
    return $this;
  }

  public function setLabel(){
    $args=func_get_args();
    if(count($args)==3){
      list($type,$name,$label)=$args;
     }else{
      list($name,$label)=$args;
      $type=$this->getType($name);
    }
    if(in_array($type,array("checkbox","radio"))){
      foreach($label as $key=>$val){
	$this->parts[$type][$name][$key]["_param"][1]=$val;
      }
    }else{
      $this->parts[$type][$name]["_param"][1]=$label;
    }
    return $this;
  }

  public function getType($name){
    return $this->_findTypeByName($name);
  }

  private function _findTypeByName($name){
    return $this->typeCache[$name];
  }

  private function _addTypeCache($name,$type){
    if(isset($this->typeCache[$name])){
      $error="同じnameの要素を設定しようとしています:{$this->typeCache[$name]}の{$name}と{$type}の{$name}";
      trigger_error($error,E_USER_WARNING);
      return false;
    }
    $this->typeCache[$name]=$type;
  }

  /**
   * 互換性を維持するために...
   */
  public function changeType($name,$type){
    $this->setType($name,$type);
    return $this;
  }

  public function setDefault($name,$value){
    $type=$this->_findTypeByName($name);
    switch($type){
    case "select":
      $this->setSelectDefault($name,$value);
      break;
    case "checkbox":
    case "radio":
      $this->setInputAttr($type,$name,"default",$value);
      break;
    default:
      $this->setInputAttr($type,$name,"value",$value);
      break;
    }
    return $this;
  }
  
  /**
   * setId,setPlaceholder,setSize,setMaxlength......
   */
  public function __call($method,$argus){
    $name=$argus[0];
    $value=$argus[1];
    $type=$this->_findTypeByName($name);
    if(strpos($method,"set")===0){
      $attr=str_replace("set","",strtolower($method));
      $this->setInputAttr($type,$name,$attr,$value);
    }
    return $this;
  }
  
  public function setExtOptions($name,$options){
    foreach($options as $attr=>$val){
      $method="set".$attr;
      $this->{$method}($name,$val);
    }
  }

  public function addText($name,$val=null){
    if(empty($val) && isset($this->data[$name])){
      $val=$this->data[$name];
    }
    $this->_addTypeCache($name,"text");
    $this->parts["text"][$name]=array(
				      "_call"=>array("self","getInput"),
				      "_param"=>array(
						      array("type"=>"text","name"=>$name,"value"=>$val)
						      )
				      );
    $this->addCheckQueue($name);
    return $this;
  }
  
 
  public function addTexts($group,$value=array(),$option=array()){
    $this->addParts("addText",$group,$value,$option);
  }

  public function addHiddens($group,$value=array(),$option=array()){
    $this->addParts("addHidden",$group,$value,$option);
  }

  public function addPasswords($group,$value=array(),$option=array()){
    $this->addParts("addPassword",$group,$value,$option);
  }
  
  public function addFiles($group,$value=array(),$option=array()){
    $this->addParts("addFile",$group,$value,$option);    
  }

  private function addParts($pmethod,$group,$value=array(),$option=array()){
    foreach($group as $key=>$name){
      $this->{$pmethod}($name,$value[$key]);
      if(isset($option[$name])){
	foreach($option[$name] as $attr=>$val){
	  $method="set".ucfirst($attr);
	  $this->{$method}($name,$val);
	}
      }
    }
    return $this;
  }

  public function addButton($name,$val){
    $this->_addTypeCache($name,"button");
    $this->parts["button"][$name]=array(
				      "_call"=>array("self","getInput"),
				      "_param"=>array(
						      array("type"=>"button","name"=>$name,"value"=>$val)
						      )
				      );
    return $this;    
  }

  public function addCheckbox($name,$val=null,$label=null,$default=null){
    if(empty($default) && isset($this->data[$name])){
      $default=$this->data[$name];
    }
    $this->_addTypeCache($name,"checkbox");
    if(!isset($this->parts["checkbox"][$name])){
      $this->parts["checkbox"][$name]=array();
    }
    if(is_array($val)){
      foreach($val as $key=>$_val){
	$this->parts["checkbox"][$name][]=array(
						"_call"=>array("self","getInput"),
						"_param"=>array(
								array("type"=>"checkbox","name"=>$name,"value"=>$_val,"default"=>$default),
								$label[$key]
								)
						);
      }
    }else{
      $this->parts["checkbox"][$name][]=array(
					      "_call"=>array("self","getInput"),
					      "_param"=>array(
							      array("type"=>"checkbox","name"=>$name,"value"=>$val,"default"=>$default),
							      $label
							      )
					      );
    }
    $this->addCheckQueue($name);
    return $this;
  }
  
  public function addRadio($name,$value=null,$label=null,$default=null){
    if(empty($default) && isset($this->data[$name])){
      $default=$this->data[$name];
    }
    $this->_addTypeCache($name,"radio");
    if(!isset($this->parts["radio"][$name])){
      $this->parts["radio"][$name]=array();
    }
    if(is_array($value)){
      foreach($value as $key=>$val){
	$this->parts["radio"][$name][]=array(
					     "_call"=>array("self","getInput"),
					     "_param"=>array(
							     array("type"=>"radio","name"=>$name,"value"=>$val,"default"=>$default),
							     $label[$key]
							     )
					     );
      }
    }else{
      $this->parts["radio"][$name][]=array(
					   "_call"=>array("self","getInput"),
					   "_param"=>array(
							   array("type"=>"radio","name"=>$name,"value"=>$value,"default"=>$default),
							   $label
							   )
					   );
    }
    $this->addCheckQueue($name);
    return $this;
  }

  public function addHidden($name,$value=null,$label=null){
    if(empty($value) && isset($this->data[$name])){
      $value=$this->data[$name];
    }
    $this->_addTypeCache($name,"hidden");
    $this->parts["hidden"][$name]=array(
					"_call"=>array("self","getInput"),
					"_param"=>array(
							array("type"=>"hidden","name"=>$name,"value"=>$value),
							$label
							)
					);
    $this->addCheckQueue($name);
    return $this;
  }

  public function addFile($name,$value=null){
    $this->_addTypeCache($name,"file");
    $this->parts["file"][$name]=array(
				      "_call"=>array("self","getInput"),
				      "_param"=>array(
						      array("type"=>"file","name"=>$name,"value"=>$value)
						      )
				      );
    $this->attrs["enctype"]="enctype='multipart/form-data'";
    $this->addCheckQueue($name);
    return $this;
  }
  
  public function addImg($name,$src,$height=null,$width=null){
    $this->_addTypeCache($name,"img");
    $this->parts["img"][$name]=array(
				       "_call"=>array("self","_getImg"),
				       "_param"=>array($name,$src,$height,$width)
				       );
    return $this;
  }

  public function setImgSrc($name,$src){
    if($this->hasParts($name,"img")){
      $this->parts["img"][$name]["_param"][1]=$src;
    }
    return $this;
  }

  public function addPassword($name,$value=null){
    if(empty($value) && isset($this->data[$name])){
      $value=$this->data[$name];
    }
    $this->_addTypeCache($name,"password");
    $this->parts["password"][$name]=array(
					  "_call"=>array("self","getInput"),
					  "_param"=>array(
							  array("type"=>"password","name"=>$name,"value"=>$value)
							  )
					  );
    $this->addCheckQueue($name);
    return $this;
  }

  public function addTextArea($name,$value=null,$row=10,$col=50){
    if(empty($value) && isset($this->data[$name])){
      $value=$this->data[$name];
    }
    $this->_addTypeCache($name,"textarea");
    $this->parts["textarea"][$name]=array(
					  "_call"=>array("self","_getTextArea"),
					  "_param"=>array(
							  array("name"=>$name,"value"=>$value,"row"=>$row,"col"=>$col)
							  )
					  );
    $this->addCheckQueue($name);
    return $this;
  }

  public function setTextAreaContent($name,$content){
    if(empty($content) && isset($this->data[$name])){
      $content=$this->data[$name];
    }
    if($this->hasParts($name,"textarea")){
      $this->parts["area"][$name]["_param"][1]=$content;
    }
    return $this;
  }

  public function addSelect($name,$value,$disp,$default=null){
    if(empty($default) && isset($this->data[$name])){
      $default=$this->data[$name];
    }
    $this->_addTypeCache($name,"select");
    $this->parts["select"][$name]=array(
					"_call"=>array("self","_getSelect"),
					"_param"=>array(
							array("name"=>$name,"value"=>$value,"option"=>$disp,"default"=>$default)
							)
					);
    $this->addCheckQueue($name);
    return $this;
  }
  
  private function setSelectDefault($name,$value){
      $this->parts["select"][$name]["_param"][0]["default"]=$value;
      return $this;
  }

  static private function _q($val){
    return "'".$val."'";
  }  

}

class form_parts extends ArrayIterator {
  private static $count=0;
  private static $index="MyFormParts";
  private $type;
  private $parts=null;
  private $multi=false;

  public function __construct($arr,$multi=false){
    $this->type=$arr["type"];
    $this->parts=$arr["parts"];
    $this->multi=$multi;
  }

  public function __toString(){
    $str=array();
    switch($this->type){
    case "checkbox":
    case "radio":
      foreach($this->parts as $parts){
	foreach($parts as $key=>$obj){
	  $str[]=call_user_func_array($obj["_call"],$obj["_param"]);
	}
      }
    break;
    default:
      foreach($this->parts as $obj){
	$str[]=call_user_func_array($obj["_call"],$obj["_param"]);
      }
      break;
    }
    if($this->multi==true){
      return $str;
    }
    return join(' ',$str);
  }

  public function offsetGet($flag){
    $items=array();
    if(!isset($this->parts[$flag])){
      trigger_error("無効なname，テンプレートを確認してください",E_USER_WARNING);
      return false;
    }
    if(isset($this->parts[$flag]["_call"])){
      $obj=$this->parts[$flag];
      $items=call_user_func_array($obj["_call"],$obj["_param"]);
    }elseif(count($this->parts[$flag])===1){
      $obj=$this->parts[$flag][0];
      $items=call_user_func_array($obj["_call"],$obj["_param"]);      
    }else{
      foreach($this->parts[$flag] as $key=>$obj){
	$items[]=call_user_func_array($obj["_call"],$obj["_param"]);
      }
      if(!$this->multi){
	$items=join("\n\r",$items);
      }
    }
    return $items;
  }
  
  private static function getId(){
    return self::$index."_".(++self::$count);
  }

  private static function getInput($attrs,$label=null){
    if($attrs["type"]=="invalid"){
      return null;
    }
    $_input=array("input");
    $_label="";
    if($attrs["default"]!==null){
      if(is_array($attrs["default"]) && in_array($attrs["value"],$attrs["default"]) ){
	$attrs["checked"]="checked";
	unset($attrs["default"]);
      }elseif($attrs["value"]==$attrs["default"]){
	$attrs["checked"]="checked";
      }
    }
    if(!empty($label)){
      if(empty($attrs["id"])){
	$attrs["id"]=self::getId();
      }
      $_label="<label for=".self::_q($attrs["id"]).">".$label."</label>";
    }
    $_input=array_merge($_input,self::_makeInput($attrs,$attrs["type"]));
    return "<".join(" ",$_input)." />".$_label;
  }
  
  private static function _makeInput($attrs,$type){
    $_attrs=array();
    if($type==="checkbox"){
      $attrs["name"]=str_replace("[]","",$attrs["name"])."[]";
    }
    foreach($attrs as $key=>$val){
      $_attrs[]=$key."=".self::_q($val);
    }
    return $_attrs;
  }

  private static function _getImg($name,$src,$height=null,$width=null){
    $img=array("<img","src=".self::_q($src));
    if(!empty($height)){
      $img[]="height=".self::_q($height);
    }
    if(!empty($width)){
      $img[]="width=".self::_q($width);
    }
    $img[]=">";
    return join(" ",$img);
  }
  
  private static function _getTextArea($attrs,$value=null,$row=10,$col=50){
    $tag=array("<textarea","name=".self::_q($attrs["name"]),"rows=".self::_q($attrs["row"]),"cols=".self::_q($attrs["col"]),">");
    return join(' ',$tag).$attrs["value"]."</textarea>";
  }

  private static function _getSelect($attrs){
    $att=array();
    foreach($attrs as $name=>$attr){
      if(!in_array($name,array("value","default","option"))){
	$att[]=$name."=".self::_q($attr);
      }
    }
    $tag="<select ".join(" ",$att)." >";
    $option=array();
    foreach($attrs["value"] as $key=>$val){
      $_opt=array();
      $_opt=array("<option","value=".self::_q($val));
      if($val==$attrs["default"]){
	$_opt[]="selected=selected";
      }
      $_opt[]=">";
      $_opt[]=$attrs["option"][$key];
      $_opt[]="</option>";
      $option[]=join(" ",$_opt);
    }
    return $tag.join("",$option)."</select>";
  }

  static private function _q($val){
    return "'".$val."'";
  }  
}