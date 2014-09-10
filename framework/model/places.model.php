<?php
class places_model extends model_core {
	##columns##
    public $columns = array(
        'id','place_id','lat','lng','name','vicinity','tel','reviews_cnt','entry','step','be_edited'
    );
    public $alter_columns = array (
  'id' => '`id` int(11) NOT NULL  AUTO_INCREMENT',
  'place_id' => '`place_id` varchar(128) NOT NULL',
  'lat' => '`lat` double NOT NULL',
  'lng' => '`lng` double NOT NULL',
  'name' => '`name` varchar(256) NOT NULL',
  'vicinity' => '`vicinity` varchar(256) NOT NULL',
  'tel' => '`tel` varchar(64) NULL',
  'reviews_cnt' => '`reviews_cnt` int(11) NOT NULL',
  'entry' => '`entry` int(11) NOT NULL',
  'step' => '`step` int(11) NOT NULL',
  'be_edited' => '`be_edited` enum(\'yes\',\'no\') NOT NULL Default \'no\'',
);
    ##columns##
	##indexes##
    public $alter_indexes = array (
  'PRIMARY' => 'PRIMARY KEY  (`id`)',
  'reference' => 'UNIQUE KEY `reference` (`place_id`)',
);
              public $primary_keys = array('`places`' => 'id');
    ##indexes##
	public $relation = array();

	public function compare_by_places($places) {
			
		$place_ids = array_column($places, "place_id");

		$tmp = array();

		foreach($places as $place) {
				
			$tmp[$place["place_id"]] = $place;
				
		}
			
		$places = $tmp;
			
		$exists = $this->find_all_by_place_id($place_ids);
			
		foreach($exists as $record) {
				
			$key = $record->place_id;
				
			$places[$key] = array_merge($places[$key], $record->to_array());
				
			unset($tmp[$key]);
		}
			
		$creates = $this->create_by_places($tmp);
		
		foreach($creates as $record) {
				
			$key = $record->place_id;
				
			$places[$key] = array_merge($places[$key], $record->to_array());
			
		}		
		
		return $places;
	}

	public function create_by_places($places) {
		$creates = array();
		
		foreach($places as $key => $place) {

			$place["be_edited"] = "no";
				
			$place["reviews_cnt"] = 0;
				
			$place["entry"] = 0;
				
			$place["step"] = 0;
			
			$creates[] = $this->create_record($place);
			
			App::model("place_attrs")->initialization($place["place_id"]);
			
			App::model("place_types")->initialization($place["place_id"]);
		}
		return $creates;
	}

	public function increase_point_by_place_id($place_id, $review) {
			
		$entry = $review["entry"] ? $review["entry"] : 0;
			
		$step = $review["step"] ? $review["step"] : 0;
			
		if($record = $this->find_by_place_id($place_id)) {
				
			$record->entry = $record->entry + $entry;
				
			$record->step = $record->step + $step;
				
			$record->reviews_cnt = $record->reviews_cnt + 1;
				
			$record->save();
				
		}

	}

	public function decrease_point_by_place_id($place_id, $review) {
			
		$entry = $review["entry"] ? $review["entry"] : 0;
			
		$step = $review["step"] ? $review["step"] : 0;
			
		if($record = $this->find_by_place_id($place_id)) {
				
			$record->entry = $record->entry - $entry;
				
			$record->step = $record->step - $step;
				
			$record->reviews_cnt = $record->reviews_cnt - 1;
				
			$record->save();
				
		}

	}

}