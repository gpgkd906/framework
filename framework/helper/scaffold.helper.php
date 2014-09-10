<?php

class scaffold_helper extends helper_core {
	private $model = null;
	private $form = null;
	private $model_info = null;
	private $action = "list";
	private $id = null;
	private $pager = null;
	private $page = 1;
	private $record = null;
	private $max_length = 10;
	private $search = array();
	private $filter = array();
	private $mask = array();
	private $auto_sort = true;
	private $controls = array("new", "add", "search", "view", "edit", "delete");
	private $extControls = array();
	private $ext_input = array();
	public $lock_action = false;
	private $labels = array("new" => "New", "add" => "Add", "search" => "Search", "view" => "View", "edit" => "Edit", "delete" => "Delete", "controls" => "controll");

	public $vars = array();
	public static $handler = array("list" => array(0, 0), "search" => array(0, 0), "view" => array(0, 0), "edit" => array(0, 0), "add" => array(0, 0), "delete" => array(0, 0), "style" => array(0, 0));
	public static $view_formater = array(
		"list" => "self::list_view",
		"search" => "self::search_view",
		"view" => "self::view_view",
		"edit" => "self::edit_view",
		"add" => "self::add_view",
		"delete" => "self::delete_view",
		"style" => "self::style_view");
	
	public function __construct() {
		if(isset($_REQUEST["scaffold_action"])) {
			$this->action = $_REQUEST["scaffold_action"];
		}
		if(isset($_REQUEST["scaffold_id"])) {
			$this->id = $_REQUEST["scaffold_id"];
		}
		if(isset($_REQUEST["page"])) {
			$this->page = $_REQUEST["page"];
		}
	}
	
	public function auto_sort($auto_sort = true) {
		$this->auto_sort = $auto_sort;
	}

	public function max_length($length = null) {
		if($length !== null) {
			$this->max_length = $length;
		}
		return $this->max_length;
	}

	public function labels($labels = null) {
		if(!is_array($labels)) {
			return false;
		}
		$this->labels = array_merge($this->labels, $labels);
	}
	
	public function label($label) {
		if(isset($this->labels[$label])) {
			return $this->labels[$label];
		}
		return $label;
	}

	public function add_filter() {
		$this->filter = array_merge($this->filter, func_get_args());
	}

	public function get_filter() {
		return $this->filter;
	}
	
	public function remove_filter() {
		$this->filter = array_diff($this->filter, func_get_args());
	}

	public function controls() {
		$this->controls = func_get_args();
	}

	public function add_ext_controls($name, $label, $link) {
		$this->extControls[$name] = array($label, $link);
	}
	
	public function has_ext_controls($name) {
		return isset($this->extControls[$name]);
	}

	public function get_ext_controls_label($name) {
		return $this->extControls[$name][0];
	}

	public function get_ext_controls_link($name) {
		return $this->extControls[$name][1];
	}

	public function get_controls() {
		return $this->controls;
	}

	public function add_mask($mask) {
		$this->mask = array_merge($this->mask, $mask);
	}

	public function get_mask($name) {
		if(isset($this->mask[$name])) {
			return $this->mask[$name];
		}
		return $name;
	}

	public function action($action = null) {
		if($action === null) {
			return $this->action;
		} else {
			$this->action = $action;
		}
	}

	public function lock_action() {
		$this->lock_action = true;
	}

	public function add_ext_input($name, $element) {
		$this->ext_input[$name] = $element;
	}

	public function ext_input() {
		return $this->ext_input;
	}

	public function page($page = null) {
		if($page === null) {
			return $this->page;
		} else {
			$this->page = $page;
		}
	}

	public function id($id = null) {
		if($id === null) {
			return $this->id;
		} else {
			$this->id = $id;
		}
	}

	public function __call($name, $param) {
		if(strpos($name, "_format") !== false) {
			$name = str_replace("_format", "", $name);
			array_unshift($param, $name);
			return call_user_func_array(array($this, "format"), $param);
		}elseif(strpos($name, "on_") !== false) {
			$name = str_replace("on_", "", $name);
			array_unshift($param, $name);
			return call_user_func_array(array($this, "handler"), $param);			
		}		
		return parent::__call($name, $param);
	}

	public function chain($action1, $action2) {
		self::$handler[$action2] = self::$handler[$action1];
		return $this;
	}
	
	public function handler() {
		$args = func_get_args();
		$action = array_shift($args);
		if(!isset(self::$handler[$action])) {
			return trigger_error("[scaffold_helper]:invalid on_action_handler name: [{$action}]", E_USER_NOTICE);
		}
		self::$handler[$action] = $args;
	}

	public function format($action = "list", $call = null) {
		if(!isset(self::$view_formater[$action])) {
			return trigger_error("[scaffold_helper]:invalid view_formater name: [{$action}]", E_USER_NOTICE);
		}
		if(is_callable($call)) {
			self::$view_formater[$action] = $call;
		}
	}
	
	public function model($model) {
		$this->model = $model;
		$this->model_info($this->model->full_columns());
		if(!in_array($this->action, array("list", "search", "view", "edit", "add", "delete"))) {
			$this->action = "list";
		}
		$this->do_action();
	}

	private function do_action() {
		$this->view_action();
		$this->search_action();
		$this->edit_action();
		$this->add_action();
		$this->delete_action();
		$this->list_action();
	}

	private function list_action() {
		if($this->action === "list") {
			$count = $this->model->count();
			$pager = App::module("page");
			$pager->request($this->page);
			$pager->display_num($this->max_length);
			$pager->items_num($count);
			$this->pager = $pager;
			list($start, $end) = $pager->get_range();
			$col = $this->model->get_primary_key();
			$from = $this->model->get_from();
			if($this->auto_sort) {
				$this->model->order("{$from}.`{$col}` desc");
			}
			$this->model->limit($start, $this->max_length);
			$this->model->remove_filter("except_self");
			$this->vars["list"] = $this->model->getall();
			$this->vars["total"] = $count;
			if(isset(self::$handler["list"][0]) && is_callable(self::$handler["list"][0])) {
				call_user_func(self::$handler["list"][0], $this, $this->model, $pager);
			}
		}
	}
	
	private function view_action() {
		if(!empty($this->id)) {
			$primary_key = $this->model->get_primary_key();
			$this->vars["record"] = $this->model->find($primary_key, $this->id)->get();
			if(isset(self::$handler["view"][0]) && is_callable(self::$handler["view"][0])) {
				call_user_func(self::$handler["view"][0], $this, $this->vars["record"]);
			}
		}
	}

	private function search_action() {
		if($this->action === "search") {
			$this->make_form();
		}
	}

	private function edit_action() {
		if($this->action === "edit") {
			if(!(isset($this->vars["record"]) && !!$this->vars["record"])) {
				$this->vars["record"] = $this->model->get();
			}
			$this->make_form();
		}
	}

	private function add_action() {
		if($this->action === "add") {
			$this->vars["record"] = $this->model->new_record();
			$this->make_form();
		}
	}
	
	private function delete_action() {
		if($this->action === "delete") {
			if($record = $this->vars["record"]) {
				$record->delete();
			}
			$this->action("list");
		}
	}
	
	private function make_form() {
		$this->form = App::module("form2")->create("scaffold");
		$action = $this->action;
		$unique = array();
		$filter = $this->get_filter();
		foreach($this->model_info as $row) {
			if(in_array($row["Field"], $filter)) {
				continue;
			}
			list($type, $ext) = isset($row["Type"]) ? $this->parse_type($row["Type"]) : array("char", null);
			switch($type) {
				case "text":case "longtext":case "tinytext":case "mediumtext":case "blob":case "longblob":case "tinyblob":case "mediumblob": 
					$elem = $this->form->append("textarea", $row["Field"]); 
					break;
				case "enum": $elem = $this->form->append("select", $row["Field"], array_combine($ext, $ext)); break;
				case "set": $elem = $this->form->append("checkbox", $row["Field"], array_combine($ext, $ext)); break;
				case "int":case "tinyint":case "smallint":case "mediumint":case "bigint": $elem = $this->form->append("number", $row["Field"]); break;
				case "datetime": case "date": $elem = $this->form->append("date", $row["Field"]); break;
				default: $elem = $this->form->append("text", $row["Field"]); break;
			}
			if(isset($row["Null"]) && strtolower($row["Null"]) == "no" && $action !== "search") {
				$elem->must_be(checker::Exists);
			}
			if(isset($row["Default"])) {
				$elem->value($row["Default"]);
			}
			if(isset($row["Key"]) && strtolower($row["Key"]) === "uni") {
				$unique[] = $row["Field"];
			}
		}
		$record = isset($this->vars["record"]) ? $this->vars["record"] : null;
		if($action === "edit") {
			$this->form->assign($record->to_array());
		}
		if(isset(self::$handler[$this->action][0]) && is_callable(self::$handler[$this->action][0])) {
			call_user_func(self::$handler[$this->action][0], $this, $this->form, $record);
		}
		$self = $this;
		$model = $this->model;
		$this->form->submit(function($data, $form) use($action, $self, $unique, $model, $record) {
				if($action === "edit" && $request_id = $self->id()) {
					$model->add_filter("except_self", $model->get_primary_key(), $request_id, "<>");
				}
				if($action !== "search" && $action !== "edit") {
					foreach($unique as $uni) {
						$model->skip_filter();
						if($_d = $model->find($uni, $data[$uni])->get_as_array()) {
							$form->{$uni}->force_error("※データが重複しています");
							return false;
						}
					}
				}
				switch($action) {
					case "edit": case "add": 
						if(isset(scaffold_helper::$handler[$action][1]) && is_callable(scaffold_helper::$handler[$action][1])) {
							call_user_func(scaffold_helper::$handler[$action][1], $data, $form, $record, $model, $self);
						}
						break;
					case "search":
						$search = array();
						$search_all = null;
						foreach($data as $key => $val) {
							if($key === "scaffold_search_all" && isset($val[0])) {
								$search_all = $val;
							} elseif (isset($val) && isset($val[0])) {
								$search[$key] = $val;
							}
						}
						if($search_all !== null) {
							$cols = $model->columns();
							$vals = array_fill(0, count($cols), $search_all);
							$model->add_filter("all search", join(",", $cols), $vals, "like");
						} else {
							if(isset(scaffold_helper::$handler["search"][1]) && is_callable(scaffold_helper::$handler["search"][1])) {
								$search = call_user_func(scaffold_helper::$handler["search"][1], $search, $model);
							}
							if(!empty($search)) {
								$model->condition($search);
							}
						}
						$self->action("list");
						break;
				}
			});
		$this->form->confirm(null, function($data, $form) use($action, $record, $self, $model){
				if($self->lock_action === false) { 
					$self->action("list");
				}
				if(!empty($data)) {
					switch($action) {
						case "edit":case "add":
							$model->remove_filter("except_self");
							$record->assign($data);
							$model_primary = $model->get_primary_key();
							$filter = $self->get_filter();
							foreach($filter as $label) {
								if($label === $model_primary) {
									continue;
								}
								if(!isset($record->{$label})) {
									$record->{$label} = "";
								}
							}
							if(isset(scaffold_helper::$handler[$action][2]) && is_callable(scaffold_helper::$handler[$action][2])) {
								call_user_func(scaffold_helper::$handler[$action][2], $data, $record, $self);								} else {
								$record->save();
							}
							break;
					}
				}
			});
	}

	public function model_info($info = null) {
		if($info !== null) {
			$deny_pri = !in_array($this->action, array("list", "view"));
			foreach($info as $key => $row) {
				if(in_array($row["Field"], array("register_dt", "update_dt"))) {
					unset($info[$key]);
				}
				if($deny_pri && isset($row["Key"]) && strtolower($row["Key"]) === "pri") {
					unset($info[$key]);
				}
			}
			$this->model_info = $info;
		}
		return $this->model_info;
	}

	private function parse_type($type) {
		preg_match("/(\w+)(?:\((.*)\))?()/", $type, $m);
		list($dummy, $type, $ext) = $m;
		if(in_array($type, array("enum", "set"))) {
			$ext = explode(",", str_replace(array("'", '"'), "", $ext));
		}
		return array($type, $ext);
	}

	public function display() {
		if(isset($this->pager)) {
			if(isset($this->action)) {
				$this->pager->add_query("scaffold_action" ,$this->action);
			}
			if(isset($this->id)) {
				$this->pager->add_query("scaffold_id" ,$this->id);
			}
			if(isset($this->page)) {
				$this->pager->add_query("scaffold_page" ,$this->page);
			}
		}
		if(is_callable(self::$view_formater["style"])) {
			call_user_func(self::$view_formater["style"], $this, App::helper("view"));
		}
		switch($this->action) {
			case "edit": call_user_func(self::$view_formater["edit"], $this, $this->form); break;
			case "search": call_user_func(self::$view_formater["search"], $this, $this->form); break;
			case "add": call_user_func(self::$view_formater["add"], $this, $this->form); break;
			case "view": call_user_func(self::$view_formater["view"], $this, $this->vars); break;
			case "delete": call_user_func(self::$view_formater["delete"], $this, App::controller()); break;
			case "list": default: call_user_func(self::$view_formater["list"], $this, $this->vars, $this->pager); break;
		}
	}
	
	private static function list_view($scaffold, $vars, $pager) {
			$filter = $scaffold->get_filter();
			$controls = $scaffold->get_controls();
			?>
			<div class="panel panel-default">
				 <div class="panel-heading"><?php echo $scaffold->label("list") ?></div>
				 <div class="panel-body">
			<div class="row">
			<div class="col-sm-6">
				 <?php if(in_array("new", $controls)) { ?>
				 <a class="btn btn-danger" href="?scaffold_action=add"><?php echo $scaffold->label("new") ?></a>
			<?php } ?>
			<?php if(in_array("search", $controls)) { ?>
			<a class="btn btn-danger" href="?scaffold_action=search"><?php echo $scaffold->label("search") ?></a>
			<?php } ?>
			<br /><br />
			</div>
			</div>
			<table class="table table-striped table-bordered table-hover" id="dataTables-example">
			<thead>
			<tr>
			<?php foreach($scaffold->model_info() as $row) { 
				if(in_array($row["Field"], $filter)) {
					continue;
				}
			 ?>
				<th><?php echo isset($row["Mask"]) ? $row["Mask"] : $scaffold->get_mask($row["Field"]) ?></th>
	<?php } ?>
		<th><?php echo $scaffold->get_mask("controll") ?></th>
			</tr>
			</thead>
			<tbody>
			<?php foreach($vars["list"] as $item) { ?>
			<tr>
			<?php foreach($scaffold->model_info() as $row) { 
				if(in_array($row["Field"], $filter)) {
					continue;
				}
			?>
				<td><?php echo mb_strimwidth($item->{$row["Field"]}, 0, 140, "...", "utf-8"); ?></td>
								   <?php } ?>
			<td style="width:33%">
				 <?php if(in_array("view", $controls)) { ?>
				 <a class="btn btn-info" href="?scaffold_action=view&scaffold_id=<?php echo $item->get_primary_key(); ?>"><?php echo $scaffold->label("view") ?></a>
			<?php } ?>
				 <?php if(in_array("edit", $controls)) { ?>
				 <a class="btn btn-info" href="?scaffold_action=edit&scaffold_id=<?php echo $item->get_primary_key() ?>"><?php echo $scaffold->label("edit") ?></a>
			<?php } ?>
				 <?php if(in_array("delete", $controls)) { ?>
				 <a class="btn btn-info" href="?scaffold_action=delete&scaffold_id=<?php echo $item->get_primary_key() ?>"><?php echo $scaffold->label("delete") ?></a>
			<?php } ?>
			<?php if($scaffold->has_ext_controls("list_ext1")) { ?>
		<a class="btn btn-info" href="<?php echo str_replace("{scaffold_id}", $item->get_primary_key(), $scaffold->get_ext_controls_link("list_ext1")); ?>"><?php echo $scaffold->get_ext_controls_label("list_ext1") ?></a>		
			<?php } ?>
			<?php if($scaffold->has_ext_controls("list_ext2")) { ?>
		<a class="btn btn-info" href="<?php echo str_replace("{scaffold_id}", $item->get_primary_key(), $scaffold->get_ext_controls_link("list_ext2")); ?>"><?php echo $scaffold->get_ext_controls_label("list_ext2") ?></a>		
			<?php } ?>
			<?php if($scaffold->has_ext_controls("list_ext3")) { ?>
		<a class="btn btn-info" href="<?php echo str_replace("{scaffold_id}", $item->get_primary_key(), $scaffold->get_ext_controls_link("list_ext3")); ?>"><?php echo $scaffold->get_ext_controls_label("list_ext3") ?></a>		
			<?php } ?>
			<?php if($scaffold->has_ext_controls("list_ext4")) { ?>
		<a class="btn btn-info" href="<?php echo str_replace("{scaffold_id}", $item->get_primary_key(), $scaffold->get_ext_controls_link("list_ext4")); ?>"><?php echo $scaffold->get_ext_controls_label("list_ext4") ?></a>		
			<?php } ?>
			<?php if($scaffold->has_ext_controls("list_ext5")) { ?>
		<a class="btn btn-info" href="<?php echo str_replace("{scaffold_id}", $item->get_primary_key(), $scaffold->get_ext_controls_link("list_ext5")); ?>"><?php echo $scaffold->get_ext_controls_label("list_ext5") ?></a>		
			<?php } ?>
				 </td>
				 </tr>
			<?php } ?>
			</tbody>
				  </table>
				  <div class="row">
				  <div class="col-sm-6">
				  <div class="dataTables_info" id="dataTables-example_info" role="alert" aria-live="polite" aria-relevant="all">Total：<?php echo $vars["total"] ?></div>
				  </div>
				  <div class="col-sm-6">
				  <div class="dataTables_paginate paging_simple_numbers" id="dataTables-example_paginate">
				  <?php $pager->show(); ?>
				  </div>
				  </div>
				  </div>
				  </div>
				  <!-- /.panel-body -->
				  </div>
				  <!-- /.panel -->
				  <?php
				  }
	
	private static function view_view($scaffold, $vars) {
		$record = $vars["record"];
		$filter = $scaffold->get_filter();
		$filter[] = "register_dt";
		$filter[] = "update_dt";
		$controls = $scaffold->get_controls();
		?>
			<div class="panel panel-default">
				 <div class="panel-heading"><?php echo $scaffold->label("view") ?></div>
			 <div class="panel-body">
			 <div class="list-group">
			 <?php foreach($record->to_array() as $key => $val) { 
				 if(in_array($key, $filter)) {
					 continue;
				 }
				 ?>
			 <span class="list-group-item">
				  <i class="fa fa-fw"></i> <?php echo nl2br($val) ?>
			<span class="pull-right text-muted small"><em><?php echo $scaffold->get_mask($key) ?></em>
			</span>
			</span>
			<?php } ?>
			</div>
				  </div>
				  <div class="panel-footer">
				 <?php if(in_array("list", $controls)) { ?>
				  <label><a class="btn btn-default" href="?scaffold_action=list"><?php echo $scaffold->label("list") ?></a></label>
			<?php } ?>
				 <?php if(in_array("new", $controls)) { ?>
				  <label><a class="btn btn-default" href="?scaffold_action=add"><?php echo $scaffold->label("new") ?></a></label>
			<?php } ?>
				 <?php if(in_array("edit", $controls)) { ?>
				  <label><a class="btn btn-default" href="?scaffold_action=edit&scaffold_id=<?php echo $record->get_primary_key() ?>"><?php echo $scaffold->label("edit") ?></a></label>
			<?php } ?>
				 <?php if(in_array("delete", $controls)) { ?>
				  <label><a class="btn btn-default" href="?scaffold_action=delete&scaffold_id=<?php echo $record->get_primary_key() ?>" onclick="return confirm(\'本当に削除しますか？\')"><?php echo $scaffold->label("delete") ?></a></label>
			<?php } ?>
			</div>
				  <!-- /.panel-body -->
				  </div>
				  <!-- /.panel -->
		<?php
				  }
	
	private static function edit_view($scaffold, $form) {
		$form->submit->value("Edit");
		self::form_view($scaffold, $form, "edit");
	}
	
	private static function add_view($scaffold, $form) {
		$form->submit->value("Add");
		self::form_view($scaffold, $form, "new");
	}

	private static function search_view($scaffold, $form) {
		$form->submit->value("Search");
		self::form_view($scaffold, $form, "search");		
	}
	
	private static function form_view($scaffold, $form, $title) {
		$filter = $scaffold->get_filter();
		?>
		<div class="panel panel-default">
			<div class="panel-heading"><?php echo $scaffold->label($title) ?></div>
			<div class="panel-body">
			<?php $form->start(); 
			 if($title === "search" && !in_array("search all", $filter)) { ?>
				<div class="form-group">
				<label><?php echo $scaffold->get_mask("search all") ?></label>
				  <input type="text" name="scaffold_search_all" class="form-control">			   
				</div>
			 <?php }
			foreach($scaffold->model_info() as $row) { 
				if(in_array($row["Field"], $filter)) {
					continue;
				}
				?>
				<div class="form-group">
				<label><?php echo isset($row["Mask"]) ? $row["Mask"] : $scaffold->get_mask($row["Field"])  ?></label>
				<?php $form->{$row["Field"]}->class("form-control"); ?>
				<?php echo $form->{$row["Field"]}->error, $form->{$row["Field"]} ?>
				</div>
				<?php }
					 foreach($scaffold->ext_input() as $name => $element) { 						 
				?>
				<div class="form-group">
				<label><?php echo $name  ?></label>
				<?php $element->class("form-control"); ?>
				<?php echo $element->error, $element ?>
				</div>
				<?php } ?>
		<div class="form-group">
			 <?php $form->submit->add_class("form-control")->value("確定"); ?>
			 <?php echo $form->submit ?>
			 </div>                          
			<?php $form->end(); ?>
			</div>
				  <!-- /.panel-body -->
				  </div>
				  <!-- /.panel -->
		<?php
			 }

	private static function style_view($scaffold, $view) {
		$view->load_css("bootstrap.css");
	}

	public function style_off() {
		self::$view_formater["style"] = null;		
	}
}