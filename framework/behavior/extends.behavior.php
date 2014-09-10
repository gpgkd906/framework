<?php

class extends_behavior extends model_behavior_core {
	
	public function has_many($row, $caller, $active = true) {
		foreach($this->has_many as $key => $config) {
			if($key !== $caller) {
				$row->{$key} = self::select_model($key)->find($config["subject"], $row->{$config["identity"]})->getall($this->table);
			}
		}
		return $row;
	}
	
	
	public function belong_to($row, $caller, $active = true) {
		foreach($this->belong_to as $key => $config) {
		  if($key !== $caller) {
			  $row->{$key} = self::select_model($key)->find($config["subject"], $row->{$config["identity"]})->get($this->table);
		  }
		}
		return $row;
	}

	public function all_has_many($rows, $caller, $active = true) {
		foreach($this->has_many as $key => $config) {
			if($key !== $caller) {
			  $ids = $this->fuck_all_ids($rows, $config["identity"]);
			  if($active) {
				  $tmp = self::select_model($key)->find($config["subject"], $ids)->getall($this->table);
				  $rows = $this->fuck_all_rows($key, $tmp, $rows, $config["identity"], $config["subject"]);
			  }
			}
		}
		return $rows;
	}
	
	public function all_belong_to($rows, $caller, $active = true) {
		foreach($this->belong_to as $key => $config) {
			if($key !== $caller) {
				$ids = $this->fuck_all_ids($rows, $config["identity"]);
				$tmp = self::select_model($key)->find($config["subject"], $ids)->get($this->table);
				$rows = $this->fuck_all_rows_single($key, $tmp, $rows, $config["identity"], $config["subject"]);
			}
		}
		return $rows;
	}
	
	private function fuck_all_ids($rows, $identity) {
	  $ids = array();
	  foreach($rows as $row) {
		  $ids[] = $row->{$identity};
	  }
	  return $ids;
	}
	
	private function fuck_all_rows_single($key, $tmp, $rows, $identity, $subject) {
		$res = array();
		foreach($tmp as $_row) {
			$res[$_row->{$config["subject"]}] = $_row;
		}
		foreach($rows as $row) {
			if(isset($res[$row->{$config["identity"]}])) {
				$row->{$key} = $res[$row->{$config["identity"]}];
			} else {
				$row->{$key} = false;
			}
		}
	}
	
	private function fuck_all_rows($key, $tmp, $rows, $identity, $subject) {
		$res = array();
		foreach($tmp as $_row) {
			$_subject = $_row->{$subject}; 
			if(!isset($res[$_subject])) {
				$res[$_subject] = array();
			}
			$res[$_subject][] = $_row;
		}
	  foreach($rows as $row) {
		  if(isset($res[$row->{$identity}])) {
			  $row->{$key} = $res[$row->{$identity}];
		  } else {
			  $row->{$key} = false;
		  }
	  }
	}
	
	public static function use_belong_to_many($config = true) {
		self::$use_belong_to_many = (boolean) $config;
	}
	
}