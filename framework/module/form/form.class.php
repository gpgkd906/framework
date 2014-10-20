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
  
	public function __construct(){
		if(!class_exists("checker")){
			controller::getSingletonInstance()->import("checker");
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
	private $genConfirmed=false;
	private $completed = false;
	private $file_group = array();
	private $file_handler = null;

	public function __construct($arr){
		$id=$arr["id"];
		$this->id=$id;
		$this->addHidden("form_id",$id);
		$this->addHidden("form_mode","edit");
		$this->addButton("submit","確認する")->setType("submit","submit")->setClass("submit","btn btn-success");
		$this->addButton("reset","リセット")->setType("reset","reset")->setClass("reset","btn-danger");
		$this->attrs["id"]="id=".self::_q($id);
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
		$isSubmit = false;
		if($this->isSubmit){
			return $this->isSubmit;
		}
		$data=$this->getData();
		if(isset($data["reset"]) && !isset($data["submit"])){
			$this->addError("reset",null);
			unset($this->data["reset"]);
			unset($this->data["submit"]);
		}
		if(isset($data["form_id"]) && $data["form_id"]==$this->id) {
			$isSubmit = true;
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
				$result=checker::myFormCheck($this->data[$name],$set);
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

	public function skip_confirm() {
		$this->setValue("form_mode", "confirm");
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
					call_user_func_array($onComplete,array($this));
					return $this->completed = true;
				}
				if($this->autoConfirm){
					$this->genConfirm($onConfirm);
				}
			}
			return $result;
		}
	}

	public function is_confirmed() {
		return $this->genConfirmed;
	}

	public function is_complete() {
		return $this->completed;
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
			if(isset($this->data["form_id"]) && $this->data["form_id"]!==$this->getFormId()){
				$this->data=array();
			}else{
				//did we got the uploaded file and the file_handler?
				if(is_callable($this->file_handler) && !empty($_FILES)) {
					foreach($_FILES as $name=>$file){
						if(isset($file["tmp_name"][0])){
							$this->data[$name] = call_user_func($this->file_handler, $file);
						}
					}
				}
			}
		}
		return $this->data;
	}

	public function on_file($handler) {
		$this->file_handler = $handler;
	}

	public function set_data($key, $name) {
		$this->data[$key] = $name;
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
		if(empty($val) && isset($_REQUEST[$name])){
			$val=$_REQUEST[$name];
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
		if(empty($default) && isset($_REQUEST[$name])){
			$default=$_REQUEST[$name];
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
		if(empty($default) && isset($_REQUEST[$name])){
			$default=$_REQUEST[$name];
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
		if(empty($value) && isset($_REQUEST[$name])){
			$value=$_REQUEST[$name];
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
		if(empty($value) && isset($_REQUEST[$name])) {
			$value = $_REQUEST[$name];
		}
		$this->_addTypeCache($name,"file");
		$this->parts["file"][$name]=array(
			"_call"=>array("self","getInput"),
			"_param"=>array(
				array("type"=>"file","name"=>$name,"value"=>$value)
			)
		);
		$this->attrs["enctype"]="enctype='multipart/form-data'";
		$this->addCheckQueue($name);
		$this->file_group[] = $name;
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
		if(empty($value) && isset($_REQUEST[$name])){
			$value=$_REQUEST[$name];
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
		if(empty($value) && isset($_REQUEST[$name])){
			$value=$_REQUEST[$name];
		}
		$this->_addTypeCache($name,"textarea");
		$this->parts["textarea"][$name]=array(
			"_call"=>array("self","_getTextArea"),
			"_param"=>array(
				array("name"=>$name,"value"=>$value,"rows"=>$row,"cols"=>$col)
			)
		);
		$this->addCheckQueue($name);
		return $this;
	}

	public function addSelect($name,$value,$disp,$default=null){
		if(empty($default) && isset($_REQUEST[$name])){
			$default=$_REQUEST[$name];
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

	//public function each

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
		//    if($attrs["default"]!==null){
		if(isset($attrs["default"])){
			if(is_array($attrs["default"]) && in_array($attrs["value"],$attrs["default"]) ){
				$attrs["checked"]="checked";
				unset($attrs["default"]);
			}elseif($attrs["value"]==$attrs["default"]){
				$attrs["checked"]="checked";
			}
		}
		$_input=array_merge($_input,self::_makeInput($attrs,$attrs["type"]));
		$_input = "<".join(" ",$_input)." />";
		if(!empty($label)){
			$_input = "<label class='form_label'>" . $_input . nl2br($label) . "</label>";
		}
		return $_input;
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
  
	private static function _getTextArea($attrs){
		$tag=array("<textarea");
		foreach($attrs as $name => $attr) {
			$tag[] = "{$name}=" . self::_q($attr);
		}
		$tag[] = ">";
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

/**
 *   Licensed under the MIT license:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 *   author: chenhan,gpgkd906@gmail.com
 *   website: http://dev.gpgkd906.com/MyProject/
 */
class checker {
	//check handler number
	const Exists=0;
	const Number=1;
	const Jtext=2;
	const Kana=3;
	const Email=4;
	const PostZip=5;
	const Tel=6;
	const Int=7;
	const Float=8;
	const Ip=9;
	const Url=10;
	const Boolean=11;
	const AlphaNum=12;
	const Jname=13;
	const TagCheck=14;
	const Roma=15;
	const Date=16;
	const NoTag=17;
	//filter
	private static $validate=array(
		"bool"=>258,
		"mail"=>274,
		"email"=>274,
		"float"=>259,
		"int"=>257,
		"ip"=>275,
		"url"=>273,
		"regexp"=>272,
	);
	private static $sanitize=array(
		"mail"=>517,
		"encode"=>514,
		"magic_quotes"=>521,
		"float"=>520,
		"int"=>519,
		"html"=>515,
		"string"=>513,
		"url"=>518,
	);
	private static $flag=array(
		"strip_low"=>4,
		"strip_hign"=>8,
		"fraction"=>4096,
		"thousand"=>8192,
		"scientific"=>16384,
		"quotes"=>128,
		"encode_low"=>16,
		"encode_hign"=>32,
		"amp"=>64,
		"octal"=>1,
		"hex"=>2,
		"IPv4"=>1048576,
		"IPv6"=>2097152,
		"no_private static "=>8388608,
		"no_res"=>4194304,
		"host"=>131072,
		"path"=>262144,
		"required"=>524288,
		"return_null"=>134217728,
		"return_array"=>60108864,
		"require_array"=>16777216,
	);
	private static $reg=array(                            
		"name"=>"/^[あ-んァ-ヾ一-龠\s]+$/",
		"mail"=>"/^[\w\-\.]+@[\w-]+(\.[\w]+)+$/",
		"email"=>"/^[\w\-\.]+@[\w-]+(\.[\w]+)+$/",
		"kana"=>"/^[あ-んァ-ヾ\s]*$/",
		"url"=>"/^https?:\/\/([^/:]+)(:(\d+))?(\/.*)?$/",
		"id"=>"/^[a-zA-Z0-9]+$/",
		"roma"=>"/^[A-Za-z\s]+$/",
		"number"=>"/^\s*(\d+([-\s]\d+)*)$/",
		"Jtext"=>"/^[あ-んァ-ヾ一-龠\w\s,、，。,.@\-]*$/",
		"filter"=>"/<[^\d](?:\"[^\"]*\"|'[^']*'|[^'\">*])*>/",
	);
  
	public static function check($val,$flag=null){
		if($flag===null){
			trigger_error("YOU SHOULD ASSIGNATION A FLAG FOR CHECKER",E_USER_NOTICE);
			return;
		}
		switch(true){
			case isset(self::$validate[$flag]):
				return self::validate($val,$flag);
				break;
			case isset(self::$reg[$flag]):
				return self::regCheck($val,$flag);
				break;
			default:
				return self::regFilter($val);
				break;
		}
	}

	public static function notNull($val){
		if(empty($val)){
			return false;
		}
		return true;
	}
  
	private static function validate($val,$flag){
		$_val=filter_var($val,self::$validate[$flag]);
		if($_val!==$val){
			return false;
		}
		return true;
	}

	private static function regCheck($val,$flag){
		if(!preg_match(self::$reg[$flag],$val)){
			return false;
		}
		return true;
	}

	private static function regFilter($val){
		if(!preg_match(self::$reg["filter"],$val)){
			return false;
		}
		return true;
	}

	public static function simple($val, $rule){
		$set = array("rule" => $rule);
		$res = self::myFormCheck($val, $set);
		if($res["status"] === "error") {
			return $res["message"];
		}
	}
  
	public static function package($post, $rules){
		$error = array();
		foreach($rules as $key => $rule) {
			if(!empty($post[$key]) && $res = self::simple($post[$key] ,$rule)) {
				$error[$key] = $res;
				unset($post[$key]);
			}
		}
		return array($post, $error);
	}

	public static function myFormCheck($val,$set){
		$res=array(
			"status"=>"success",
			"message"=>$set["message"]
		);
		switch($set["rule"]){
			case self::Exists:
				if(!self::notNull($val)){
					$res["status"]="error";
					$res["message"]=$res["message"]?$res["message"]:"※必須項目";
				}
				break;
			case self::Number:
				if(!self::regCheck($val,"number")){
					$res["status"]="error";
					$res["message"]=$res["message"]?$res["message"]:"※数字を入力してください";
				}
				break;
			case self::Jtext:
				if(!self::regCheck($val,"Jtext")){
					$res["status"]="error";
					$res["message"]=$res["message"]?$res["message"]:"※日本語文章を入力してください";
				}
				break;
			case self::Kana:
				if(!self::regCheck($val,"kana")){
					$res["status"]="error";
					$res["message"]=$res["message"]?$res["message"]:"※ふりがなを入力してください";
				}
				break;
			case self::Email:
				if(!self::validate($val,"mail")){
					$res["status"]="error";
					$res["message"]=$res["message"]?$res["message"]:"※不正なメールアドレス形式";
				}
				break;
			case self::PostZip:
				if(!self::regCheck($val,"number")){
					$res["status"]="error";
					$res["message"]=$res["message"]?$res["message"]:"※不正な郵便番号";
				}
				break;
			case self::Tel:
				if(!self::regCheck($val,"number")){
					$res["status"]="error";
					$res["message"]=$res["message"]?$res["message"]:"※不正な電話番号";
				}
				break;
			case self::Int:
				if(!self::validate($val,"int")){
					$res["status"]="error";
					$res["message"]=$res["message"]?$res["message"]:"※不正な入力値";
				}      
				break;
			case self::Float:
				if(!self::validate($val,"float")){
					$res["status"]="error";
					$res["message"]=$res["message"]?$res["message"]:"※不正な入力値";
				}            
				break;
			case self::Ip:
				if(!self::validate($val,"ip")){
					$res["status"]="error";
					$res["message"]=$res["message"]?$res["message"]:"※不正なIPアドレス";
				}                  
				break;
			case self::Url:
				if(!self::validate($val,"url")){
					$res["status"]="error";
					$res["message"]=$res["message"]?$res["message"]:"※不正なURL";
				}                        
				break;
			case self::Boolean:
				if(!self::validate($val,"url")){
					$res["status"]="error";
					$res["message"]=$res["message"]?$res["message"]:"※不正な値";
				}                              
				break;
			case self::AlphaNum:
				if(!self::regCheck($val,"id")){
					$res["status"]="error";
					$res["message"]=$res["message"]?$res["message"]:"※英数字だけを入力してください";
				}
				break;
			case self::Roma:
				if(!self::regCheck($val,"roma")){
					$res["status"]="error";
					$res["message"]=$res["message"]?$res["message"]:"※ローマ字を入力してください";
				}
				break;
			case self::Jname:
				if(!self::regCheck($val,"name")){
					$res["status"]="error";
					$res["message"]=$res["message"]?$res["message"]:"※漢字かがなを入力してください";
				}
				break;
			case self::Date:
				$vals = preg_split("/年|月|日|-|\//", $val);
				foreach($vals as $v) {
					if(empty($v)) {
						continue;
					}
					if(!self::regCheck($v,"number")){
						$res["status"]="error";
						$res["message"]=$res["message"]?$res["message"]:"※正しい日付を入力してください";
						break;
					}
				}
				break;
			case self::TagCheck:
			default:
				if(is_callable($set["rule"])){
					$res=call_user_func_array($set["rule"],$val);
				}elseif(!self::regFilter($val)){
					$res["status"]="error";
					$res["message"]=$res["message"]?$res["message"]:"※不正な文章";	
				}
				break;
		}
		return $res;
	}
  
}