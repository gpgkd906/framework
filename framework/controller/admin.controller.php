<?php 

class admin_controller extends application {

	public function before_action() {
		if($this->get_action() === "index") {
			$this->route->redirect("admin/dashboard");			
		}
		parent::before_action();
		if(!$this->auth->is_valid()) {
			$this->route->redirect("account/login");
		}
		$this->auth->under_admin(function() {
				$this->route->redirect("my");
			});
		$scaffold = App::helper("scaffold");
		$scaffold->labels(array("view" => "詳細", "list" => "一覧", "edit" => "変更", "search" => "検索", "new" => "追加"));
		$scaffold->add_mask(array("account" => "アカウント", "name" => "お名前","id" => "ID", "label" => "ラベル", "search all" => "全検索(何でも検索、他の項目と併用不可)"));
		$scaffold->add_filter("id", "account_id", "password", "salt", "permission", "salt", "status");
	}
	
	public function dashboard() {
		$account_id = $this->auth->id;
		$this->assign(get_defined_vars());		
	}
	
	public function category($id = null) {
		if($id !== null) {
			$category = App::model("cates")->find_by_id($id, true);
			$parent = App::model("cates")->find_by_id($category["cparent"], true);
			$scaffold = App::model("threads")->scaffold(array("category" => $id));
			$this->set_template("category_list");
		} else {
			$category = App::model("cates")->all_item();
		}
		$this->assign(get_defined_vars());
	}
}