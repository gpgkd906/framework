<?php
class category_model extends model_core {
	##columns##
    public $columns = array(
        'id','cname','ctype','cparent'
    );
    public $alter_columns = array (
  'id' => '`id` int(11) NOT NULL  AUTO_INCREMENT',
  'cname' => '`cname` varchar(255) NOT NULL',
  'ctype' => '`ctype` enum(\'level1\',\'level2\') NOT NULL',
  'cparent' => '`cparent` int(11) NOT NULL',
);
    ##columns##
	##indexes##
    public $alter_indexes = array (
  'PRIMARY' => 'PRIMARY KEY  (`id`)',
  'cparent' => ' KEY `cparent` (`cparent`)',
);
              public $primary_keys = array('`category`' => 'id');
    ##indexes##
	##relation##
	public $has_many = array();
	public $belong_to = array();
    ##relation##
	public $relation = array();

	public function list_item($id = null) {
		$parent = $this->find("id", $id)->get_as_array();
		$childs = $this->find("cparent", $id)->getall_as_array();
		$childs = $this->get_count($childs);
		$parent["cname"] = "すべて";
		$parent["count"] = 0;
		foreach($childs as $child) {
			$parent["count"] += $child["count"];
		}
		$childs[] = $parent;
		return $childs;
	}

	public function all_item() {
		$all = $this->order("cparent asc, id asc")->getall_as_array();
		$all = $this->get_count($all);
		$parents = $labels = $cates = array();
		foreach($all as $cate) {
			if($cate["ctype"] === "level1" && !isset($cates[$cate["id"]])) {
				$cates[$cate["id"]] = array();
				$labels[$cate["id"]] = $cate["cname"];
				$cate["count"] = 0;
				$cate["label"] = "すべて";
				$parents[$cate["id"]] = $cate;
			} else {
				$cate["label"] = $cate["cname"];
				$cates[$cate["cparent"]][] = $cate;
				$parents[$cate["cparent"]]["count"] += $cate["count"];
			}
		}
		foreach($cates as $cparent => $childs) {
			$cates[$cparent][] = $parents[$cparent];
		}
		return array_combine($labels, $cates);
	}

	public function get_count($categorys, $all = false) {
		$cates = array();
		$res = array();
		foreach($categorys as $category) {
			if($all || $category["ctype"] === "level2") {
				$cates[] = $category["id"];
				$res[$category["id"]] = 0;
			}
		}
		$res = App::model("threads")->count_value_by_category($cates);
		foreach($categorys as $key => $category) {
			if($all || $category["ctype"] === "level2") {
				$categorys[$key]["count"] = $res[$category["id"]];
			}
		}
		return $categorys;
	}
	
	public function get_posts($id, $limit = 10, $offset = null) {
		$target = $this->find_by_id($id, true);
		if($target["ctype"] === "level1") {
			$tmp = $this->find_all_by_cparent($id, true);
			$id = array();
			foreach($tmp as $t) {
				$id[] = $t["id"];
			}
		}
		return App::model("threads")->list_by_category($id, $limit, $offset);
	}
}