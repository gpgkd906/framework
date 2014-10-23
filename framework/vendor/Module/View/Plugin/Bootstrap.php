<?php
namespace Module\View\Plugin;

class Bootstrap {
	private static $ld="<{";
	private static $rd="}>";
	public static function tag($ld="<{",$rd="}>"){
		self::$ld=$ld;
		self::$rd=$rd;    
	}
  
	public static function stylesheet($tag="default"){
		$style="body {padding-top:40px;padding-bottom:40px;background-color:#f5f5f5;position:relative}";
		return $style;
	}
	/**
	   {bootstrap:warn message=message}
	   @message string
	*/
	public static function warn($message=null){
		$html='<div class="alert alert-block"><button class="close" data-dismiss="alert">&times;</button>'
			.'<strong>Warning!</strong>'.$message.'</div>';
		return $html;
	}

	public static function error($message=null){
		$html='<div class="alert alert-error alert-block"><button class="close" data-dismiss="alert">&times;</button>'
			.'<strong>Error!</strong>'.$message.'</div>';
		return $html;
	}
  
	public static function success($message=null){
		$html='<div class="alert alert-success alert-block"><button class="close" data-dismiss="alert">&times;</button>'
			.'<strong>Success!</strong>'.$message.'</div>';
		return $html;
	}
  
	/**
	   {bootstrap:label value=value status=status}
	*/
	public static function label($value,$status=""){
		if(strpos($value,"$")===0){
			$value=self::$ld.$value.self::$rd;
		}
		$html='<span class="label label-'.$status.'">'.$value.'</span>';
		return $html;
	}

	/**
	   {bootstrap:badge value=value status=status}
	*/
	public static function badge($value,$status=""){
		if(strpos($value,"$")===0){
			$value=self::$ld.$value.self::$rd;
		}
		$html='<span class="badge badge-'.$status.'">'.$value.'</span>';
		return $html;
	}

	/**
	   {bootstrap:searchForm name=name placeholder=placeholder}
	   @name string
	   @placeholder string
	*/
	public static function searchForm($name,$placeholder=null,$selector=""){
		$html='<form class="navbar-search pull-left"'.$selector.'><input name="'.$name.'" class="search-query" placeholder="'.$placeholder.'" type="text"/></form>';
		return $html;
	}

	/**
	   {bootstrap:progress value=value}
	   @value number/percent
	*/
	public static function progress($value=null,$selector=""){
		$html='<div class="progress"><div class="bar bar-success" style="width:'.$value.'%;"'.$selector.'></div></div>';
		return $html;
	}
  
	/**
	   {bootstrap:pageHeader title=title subtitle=subtitle}
	   @title string
	   @subtitle string
	*/
	public static function pageHeader($title,$subtitle){
		$html='<div class="page-header"><h1>'.$title.'<small>'.$subtitle.'</small></h1></div>';
		return $html;
	}
  
	/**
	   {bootstrap:menu link=$link display=display}
	   @link variables(array)
	   @display string/null
	*/
	public static function menu($link,$display="none"){
		$html="<{echo chemplate_bootstrap::action('menu',{$link},'{$display}')}>";
		return $html;
	}

	/**
	   {bootstrap:button button=$button size=size}
	   @button variables/string
	   @size string/null
	*/
	public static function button($button,$size="small"){
		if(strpos($button,'$')!==0){
			$button="'".$button."'";
		}
		$html="<{echo chemplate_bootstrap::action('button',{$button},'{$size}')}>";
		return $html;    
	}

	/**
	   {bootstrap:buttonmenu button=$button position=position}
	   @button variables
	   @position string
	*/
	public static function buttonmenu($button,$position="bottom,left"){
		$html="<{echo chemplate_bootstrap::action('buttonmenu',{$button},'{$position}')}>";
		return $html;        
	}

	/**
	   {bootstrap:tabs tabs=$tabs}
	   @tabs variables
	*/
	public static function tabs($tabs){
		$html="<{echo chemplate_bootstrap::actionTabs({$tabs})}>";
		return $html;        
	}
  
	/**
	   {bootstrap:breadcrumbs path=$path}
	   @path variables
	*/
	public static function breadcrumbs($path){
		$html="<{echo chemplate_bootstrap::action('breadcrumbs',{$path})}>";
		return $html;            
	}

	/**
	   {bootstrap:navbar nav=$nav active=active fix=fix}
	*/
	public static function navbar($nav,$active,$fix="top"){
		if(strpos($active,"$")!==0){
			$active="'".$active."'";
		}
		$html="<{echo chemplate_bootstrap::actionNavbar({$nav},{$active},'{$fix}')}>";
		return $html;            
	}

	public static function action(){
		$params=func_get_args();
		$tag=array_shift($params);
		$param=array_shift($params);
		switch($tag){
			case "menu":
				$display=array_shift($params);
				return self::actionMenu($param,$display);
				break;
			case "button":
				if(is_string($param)){
					$param=explode(",",$param);
				}
				if($size=array_shift($params)){
					$size="btn-".$size;
				}
				return self::actionButton($param,$size);
				break;
			case "buttonmenu":
				$position=explode(',',array_shift($params));
				if(in_array("top",$position)){
					$top="dropup";
				}
				if(in_array("right",$position)){
					$right="pull-right";
				}    
				return self::actionButtonMenu($param,$top,$right);
				break;
			case "tabs":
				return self::actionTabs($param);
				break;
			case "breadcrumbs":
				return self::actionBreadcrumbs($param);
				break;
			case "navbar":
				$active=array_shift($params);
				$fix=array_shift($params);
				return self::actionNavbar($param,$active,$fix);
				break;
			default:
				//do nothing
				break;
		}
	}

	public static function actionMenu($param,$display){
		$html='<div class="dropdown clearfix">'
			.'<ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu" style="display:'.$display.'">';
		foreach($param as $label=>$link){
			if(is_int($label)){
				$html.='<li class="divider"></li>';
			}else{
				$html.='<li><a tabindex="-1" href="'.$link.'">'.$label.'</a></li>';
			}
		}
		$html.='</ul></div>';
		return $html;
	}

	public static function actionButton($param,$size){
		$html='<div class="btn-group">';
		foreach($param as $p){
			$html.='<button class="btn '.$size.'">'.$p.'</button>';
		}
		$html.='</div>';
		return $html;
	}

	public static function actionButtonMenu($param,$top="",$right=""){
		$html="";
		foreach($param as $label=>$buttons){
			$html.='<div class="btn-group '.$top.'"><button class="btn">'.$label.'</button><button class="btn dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>';
			$html.='<ul class="dropdown-menu '.$right.'">';
			foreach($buttons as $button=>$href){
				$html.='<li><a tabindex="-1" href="'.$href.'">'.$button.'</a></li>';
			}
			$html.='</ul></div>';
		}
		return $html;
	}
  
	public static function actionTabs($param,$prefix="tabs"){
		$html='<div class="tabbable"><ul class="nav nav-tabs">';
		$cnt=1;
		foreach($param as $title=>$content){
			$active=$cnt===1?"active":"";
			$html.='<li class="'.$active.'"><a href="#'.$prefix.$cnt.'" data-toggle="tab">'.$title.'</a></li>';
			$cnt++;
		}
		$html.='</ul><div class="tab-content">';
		$cnt=1;
		foreach($param as $title=>$content){    
			$active=$cnt===1?"active":"";
			$html.='<div class="tab-pane '.$active.'" id="'.$prefix.$cnt.'"><p>'.$content.'</p></div>';
			$cnt++;
		}
		$html.='</div></div>';
		return $html;
	}

	public static function actionBreadcrumbs($path){
		$html='<ul class="breadcrumb">';
		$last=array_pop($path);
		foreach($path as $label=>$link){
			$html.='<li><a href="'.$link.'">'.$label.'</a><span class="divider">/</span></li>';
		}
		$html.='<li class="active">'.$last.'</li>';
		$html.='</ul>';
		return $html;
	}

	public static function actionNavbar($nav,$active,$fix="navbar-static-top"){
		if($fix==="top"){
			$fix="navbar-fixed-top";
		}elseif($fix==="bottom"){
			$fix="navbar-fixed-bottom";
		}
		$html='<div class="navbar navbar-inverse '.$fix.'"><div class="navbar-inner"><div class="container"><ul class="nav">';
		foreach($nav as $label=>$link){
			if($label===$active){
				$onActive="active";
			}else{
				$onActive="";
			}
			$html.='<li class="'.$onActive.'"><a href="'.$link.'">'.$label.'</a></li>';
		}
		$html.='</ul></div></div></div>';
		return $html;
	}

}

