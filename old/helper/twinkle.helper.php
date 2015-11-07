<?php
/**
 * もう一つの自動フォーム生成
 * adapterベースのフォーム自動生成
 * 各モデルでadapterを定義する必要があるが
 * フォーム生成においては実運用上はscaffoldより単純?
 */
class twinkle_helper extends helper_core {
	private $core = null;
	private $form = null;
	private $info = null;

	public function __construct() {
		$this->core = App::module("form2");
	}

/**
 * 自動フォーム生成
 * @param string $target 識別id
 * @param closure $handler 処理用ハンドラー
 * @param boolean $confirm 確認モードにするかしないか
 * @return
 */
	public function make($target, $handler, $param = null, $confirm = null) {
		$this->form = $form = $this->core->create("custom");
		$form->img_size("300px");
		$file = App::helper("file");
		$info = call_user_func($handler, $target, $param);
		$visible = $deleteble = array();
		if(empty($info)) {
			return $this;
		}
		if($info === true) {
			$info = array();
		}
		foreach($info as $name => $item) {
			if($item["type"] === "file") {
				$_POST[$name] = $file->save($name);
				if(isset($item["value"])){
					$_file = App::model("files")->find_one($item["value"]);
				} else {
					$_file = array("size" => null, "mime" => null, "link" => null, "path" => null, "filename" => null, "file" => null);
				}
				$changed = false;
				if(!empty($_POST[$name])) {
					foreach($_POST[$name] as $_pk => $_pv) {
						if($_file[$_pk] !== $_pv) {
							$changed = true;
							break;
						}
					}
				} 
				if(!$changed) {
					$_POST[$name] = $_file;
				}
			}
			if(!isset($item["value"])) {
				$item["value"] = null;
			}
			if(!isset($item["default"])) {
				$item["default"] = null;
			}			
			$form->append($item["type"], $name, $item["value"], $item["default"])->add_class("form-control");
			if(isset($item["null"]) && $item["null"] === false) {
				$form->{$name}->must_be(checker::Exists, "※必須項目：" . str_replace("※", "", $item["label"]));
			}
			if($item["type"] === "mail") {
				$form->{$name}->must_be(checker::Email, "※" . str_replace("※", "", $item["label"]) . "(email)が正しくありません");
			}
			if(isset($item["visible"])) {
				if(isset($_POST["visible"][$name])) {
					$item["visible"] = $_POST["visible"][$name];
				}
				$visible[$name] = $form->isolate("radio", "visible[{$name}]", array("表示" => 1, "非表示" => 0), $item["visible"]);
			}
			if(isset($item["deleteble"])) {
				$deleteble[$name] = $item["deleteble"];
			}
		}
		$form->visible = $visible;
		$form->deleteble = $deleteble;
		$this->info = $info;
		$form->confirm($confirm, function($data, $form) use ($target, $info, $handler, $param) {
				$typeble = $twinkle_add = $twinkle_delete = $fixable = $visible = array();
				if(isset($data["fixable"])) {
					$fixable = $data["fixable"];
					unset($data["fixable"]);
				}
				if(isset($data["visible"])) {
					$visible = $data["visible"];
					unset($data["visible"]);
				}
				if(isset($data["twinkle_add"])) {
					$twinkle_add = $data["twinkle_add"];
					unset($data["twinkle_add"]);
				}
				if(isset($data["twinkle_delete"])) {
					$twinkle_delete = $data["twinkle_delete"];
					unset($data["twinkle_delete"]);
				}
				if(isset($data["typeble"])) {
					$typeble = $data["typeble"];
					unset($data["typeble"]);
				}
				foreach($info as $name => $item) {
					if($item["type"] === "file" && isset($data[$name])) {
						if(!empty($data[$name]["size"])) {
							$data[$name] = App::model("files")->write_record($data[$name])->id;
						} else {
							$data[$name] = null;
						}
					}
					if(isset($data[$name])) {
						$info[$name]["value"] = $data[$name];
					}
					if(isset($fixable[$name])) {
						$info[$name]["label"] = $fixable[$name];
					}
					if(isset($visible[$name])) {
						$info[$name]["visible"] = $visible[$name];
					}
					if(isset($typeble[$name])) {
						$info[$name]["type"] = $typeble[$name];
					}
				}
				foreach($twinkle_add as $name => $value) {
					$item = $this->twinkle_init();
					if(isset($fixable[$name])) {
						$item["label"] = $fixable[$name];
					}
					if(isset($visible[$name])) {
						$item["visible"] = $visible[$name];
					}					
					if(isset($typeble[$name])) {
						$item["type"] = $typeble[$name];
					}
					$item["value"] = $value;
					$info[$name] = $item;
					$data[$name] = $value;
					$form->append($item["type"], $name, $item["value"], $item["default"])->add_class("form-control");
				}
				foreach($twinkle_delete as $name => $value) {
					if(isset($info[$name])) {
						unset($info[$name]);
					}
					if(isset($data[$name])) {
						unset($data[$name]);
					}
					$form->detach($name);
				}
				$this->info = $info;
				call_user_func($handler, $target, $param, $data, $info);
			});
		return $this;
	}

	public function filter_image($info) {
		$tmp = $swap = $file_ids = array();
		foreach($info as $name => $item) {
			if($item["type"] === "file") {
				if(isset($item["value"])){
					$file_ids[] = $item["value"];
				}
			}
		}		
		$tmp = App::model("files")->find("id", $file_ids)->getall_as_array();
		foreach($tmp as $row) {
			$swap[$row["id"]] = $row;
		}
		foreach($info as $name => $item) {
			if($item["type"] === "file") {
				if(isset($item["value"]) && isset($swap[intval($item["value"])])){
					$_file = $swap[$item["value"]];
				} else {
					$_file = array("size" => null, "mime" => null, "link" => null, "path" => null, "filename" => null, "file" => null);
				}
				$info[$name]["file"] = $_file;
			}
		}
		return $info;
	}

	public function twinkle_init() {
		return array("type" => "text", "null" => true, "label" => "", "fixable" => true, "visible" => 1, "deleteble" => true, "default" => null);
	}

	public function display($format = null, $elem_format = null) {
		$form = $this->form;
		$info = $this->info;
		if($format !== null && is_callable($format)) {
			call_user_func($format, $form, $info);
		} else {
			self::form_view($form, $info, $elem_format);
		}
	}

	public static function form_view($form, $info, $elem_format) {
		$form->set("class", "twinkle_form");
		if($form->completed()) { ?>
			<div class="twinkle twinkle_complete alert alert-success">送信完了しました.<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button></div>
		<?php }
		$form->start();
		$visible = $form->visible;
		$deleteble = $form->deleteble;
		if(isset($elem_format) && is_callable($elem_format)) {
			$form->each(function($name, $item) use($info, $visible, $deleteble, $form, $elem_format) {
					$fixable = isset($info[$name]["fixable"]) && $info[$name]["fixable"];
					$visi = isset($visible[$name]);
					$delete = isset($deleteble[$name]) && $deleteble[$name];
					call_user_func($elem_format, $name, $item, $info[$name], $form->confirmed(), $fixable, $visi, $delete);
				});
		} else {
			$form->each(function($name, $item) use($info, $visible, $deleteble, $form) { ?>
					<div class="form-group">
						<label style="display:inline">
						<label<?php if(isset($info[$name]["fixable"]) && $info[$name]["fixable"]){ ?> data-fixable="<?php echo $name ?>" style="background-color:#bce8f1"<?php } ?>><?php echo $info[$name]["label"]; ?></label>
					<?php if(isset($info[$name]["fixable"]) && $info[$name]["fixable"]){ ?>
					<input type="hidden" name="fixable[<?php echo $name ?>]" value="<?php echo $info[$name]["label"] ?>"><?php } ?>
					<?php if(isset($visible[$name]) && !$form->confirmed()) {
					echo $visible[$name];
				}
				if(isset($deleteble[$name]) && $deleteble[$name]) { ?>
					<i class="fa fa-times pull-right twinkle twinkle_delete" data-deleteble="<?php echo $name ?>"></i>
						<?php } ?>
				</label>
					  <?php echo $item->error, $item; ?>
					  </div>
					  <?php });
					  }  ?>
		<div class="form-group"><?php 
			 $form->submit->value("更新")->add_class("form-control"); 
		echo $form->submit; 
		?></div><?php                          
		$form->end();
	}
}