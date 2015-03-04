<?php 

class csv_helper extends helper_core {
	private $csv = null;
	private $form = null;
	private $key = 0;

	public function __construct() {
		$this->csv = App::module("csv");
	}
	
	public function uploader($model, $name = null, $formatter = null) {
		if(empty($name)) {
			$name = "csv" . $this->key++;
		}
		$csv_reader = $this->csv;
		$csv_reader->encode("shift_jis", "utf8");
		if($this->form === null) {
			$this->form = App::helper("form");
		}
		return $this->form->csv_upload($name, function($file) use($csv_reader) {
				$csv_reader->open($file["tmp_name"]);
				return $csv_reader;
			}, function($csv) use($model, $formatter) {
				$data = $csv->getData();
				while($row = $data["csv"]->get()) {
					if($formatter !== null) {
						$row = call_user_func($formatter, $row);
					}
					if($row) {
						$record = $model->new_record();
						$record->bind_array($row);
						$record->save();
					}
				}
			});
	}

}