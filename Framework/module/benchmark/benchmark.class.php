<?php
/**
 *
 *   Licensed under the MIT license:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 *   author: chenhan,gpgkd906@gmail.com
 *   website: http://dev.gpgkd906.com/MyProject/
 */
class benchmark {
	private $point;
	private $key = 0;
	private $option = array(
		"断点", "总时间", "分子时间", "内存消耗"
	);
  
	public function __construct(){
		$this->set("start");
	}
  
	public function set($name = null){
		$key = $this->key = $this->key+1;
		if(!$name) {
			$this->point[$key]["key"] = "Breakpoint_" . $key;
		} else {
			$this->point[$key]["key"] = $name;
		}
		$this->point[$key]["time"] = microtime(true);
		if($key === 1) {
			$this->point[1]["totaltime"] = $this->point[1]["showtime"] = sprintf('%0.5f' ,0);
		} else {
			/* $this->point[$key]["showtime"] = sprintf('%0.5f', round(($this->point[$key]["time"] - $this->point[$key-1]["time"]) * 1000, 0)); */
			/* $this->point[$key]["totaltime"] = sprintf('%0.5f', round(($this->point[$key]["time"] - $this->point[1]["time"]) * 1000, 0)); */
			$this->point[$key]["showtime"] = sprintf('%0.5f', ($this->point[$key]["time"] - $this->point[$key-1]["time"]) * 1000);
			$this->point[$key]["totaltime"] = sprintf('%0.5f', ($this->point[$key]["time"] - $this->point[1]["time"]) * 1000);
		}
		$this->point[$key]["usage"] = sprintf('%01.2f Byte', memory_get_usage() );
		$this->point[$key]["usage_human"] = sprintf('%01.2f MB', memory_get_usage() / 1048576);
	}
  
	public function display(){
		$this->set("display");
		echo "<br/><br/><div>以下のベンチ統計情報はgpgkd906.benchmarkにより生成しました<br/>";
		echo "<table border='1' class='table table-bordered'>";
		echo "<tr>";
		echo "<th>ブレークポイント</th>";
		echo "<th>総時間(ミニ秒)</th>";
		echo "<th>単位時間(ミニ秒)</th>";
		echo "<th>メモリ消費(バイト)</th>";
		echo "<th>メモリ消費(メガバイト)</th>";
		echo "</tr>";
		foreach($this->point as $key=>$value){
			echo "<tr>";
			echo "<td>".$value["key"]."</td>";
			echo "<td>".$value["totaltime"]."</td>";
			echo "<td>".$value["showtime"]."</td>";
			echo "<td>".$value["usage"]."</td>";
			echo "<td>".$value["usage_human"]."</td>";
			echo "</tr>";
		}
		echo "</table></div>";
	}
}