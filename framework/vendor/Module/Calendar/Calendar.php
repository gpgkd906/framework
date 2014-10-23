<?php
namespace Module\Calendar;
/**
 *
 *   Licensed under the MIT license:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 *   author: chenhan,gpgkd906@gmail.com
 *   website: http://dev.gpgkd906.com/MyProject/
 */
class Calendar implements IteratorAggregate {
  public $mode=CAL_GREGORIAN;
  public $year;
  public $month;
  public $day;
  public $date;
  public $dayInWeek;
  private $label;
  public $days;
  private static $_week=array("日","月","火","水","木","金","土");

  public function __construct($y=null,$m=null,$d=null){
    $this->year= isset($y)?$y:date("Y");
    $this->month= isset($m)?$m:date("m");
    $this->day= isset($d)?$d:date("d");
	$this->days = date("t") | 0;
    $this->format();
  }
  
  public function format($format="Y-m-d",$y=null,$m=null,$d=null){
    $y=isset($y) ? $y : $this->year;
    $m=isset($m) ? $m : $this->month;
    $d=isset($d) ? $d : $this->day;
    $this->assign($y . "-" . $m . "-" . $d);
    return date($format, strtotime($this->label));
  }

  public function assign($date){
    $tmsp = strtotime($date);
    $this->year = date("Y",$tmsp);
    $this->month = date("m",$tmsp);
    $this->day = date("d",$tmsp);
	$this->date = date("w",$tmsp);
    $this->dayInWeek = self::$_week[$this->date];
	$this->label = $date;
  }

  public function setYear($y){
    if($this->year!=$y){
      $this->year=$y;
      $this->format();
      $this->daysInMonth();
    }
  }

  public function getYear(){
    return $this->year;
  }

  public function setMonth($m){
    if( ($m<1) || ($m>12) ){
      return false;
    }
    if($this->month!=$m){
      $this->month=$m;
      $this->day="01";
      $this->format();
      $this->daysInMonth();
    }
  }

  public function getMonth(){
    return $this->month;
  }

  public function setDay($day){
    if( ($day<1) || ($day>$this->days) ){
      return false;
    }
    if($this->day!=$day){
      $this->day=$day;
      $this->format();
    }
  }

  public function getDay(){
    return $this->day;
  }

  public function getDate() {
	  return $this->date;
  }

  public function getLabel() {
	  return $this->label;
  }

  public function get_week($format = "Y-m-d", $date = null) {
	  if(empty($date)) {
		  $date = $this->date;
	  }
	  $range = array();
	  $start = strtotime("- " . $this->date . " days " . $this->label);
	  for($i = 0; $i < 7; $i ++) {
		  $range[] = date($format, $start);
		  $start = $start + 86400;
	  }
	  return $range;
  }

  public function setWeekArray($week){
    self::$_week=$week;
  }

  public static function getWeekArray($week){
    return self::$_week;
  }

  public function nextDay($format="Y-m-d",$noStop=false){
    $data=$this->package();
    $this->label=date($format,strtotime("+1 day ".$this->label));
    $this->assign($this->label);
    if(!$noStop){
		if($this->year!=$data["year"] || $this->month!=$data["month"]){
		  return false;
      }
    }
    return $this->package();
  }

  public function prevDay($format="Y-m-d",$noStop=false){
    $data=$this->package();
    $this->label=date($format,strtotime("-1 day ".$this->label));
    $this->assign($this->label);
    if(!$noStop){
      if($this->year!=$data["year"] || $this->month!=$data["month"]){
	return false;
      }
    }
    return $this->package();
  }

  public function nextMonth($format="Y-m-d",$noStop=false){
    $data=$this->package();
    $this->label=date($format,strtotime("+1 month ".$this->label));
    $this->assign($this->label);
    if(!$noStop){
      if($this->year!=$data["year"] || $this->month!=$data["month"]){
	return false;
      }
    }
    return $this->package();    
  }

  public function prevMonth($format="Y-m-d",$noStop=false){
    $data=$this->package();
    $this->label=date($format,strtotime("-1 month ".$this->label));
    $this->assign($this->label);
    if(!$noStop){
      if($this->year!=$data["year"] || $this->month!=$data["month"]){
	return false;
      }
    }
    return $this->package();
  }

  public function package(){
    return array(
		 "year"=>$this->year,
		 "month"=>$this->month,
		 "day"=>$this->day,
		 "in_week" => $this->date,
		 "dayInWeek"=>$this->dayInWeek,
		 "date"=>$this->label,
		 );
  }
  //Iterator
  public function getIterator(){
    $days=array(
		$this->package()
		);
    while($row=$this->nextDay()){
      $days[]=$row;
    }
    return new ArrayIterator($days);
  }

  public function create($cellCallback){
    $this->setDay("01");
    $cell=array("<tr>");
    $dow=0;
    foreach($this as $day){
      if($dow===7){
		  $dow=0; 
      }
      while(self::$_week[$dow]!==$day["dayInWeek"]){
		  $cell[]=call_user_func_array($cellCallback,array(null,$dow));
		  $dow++;
      }
      $_cell=call_user_func_array($cellCallback,array($day,$dow));
      if($dow==6){
		  $_cell.="</tr><tr>";
      }
      $cell[]=$_cell;
      $dow++;
    }
    while($dow!==7){
		$cell[]=call_user_func_array($cellCallback,array(null,$dow));
		$dow++;
    }
    $cell[]="</tr>";
    return join("",$cell);
  }
  
}