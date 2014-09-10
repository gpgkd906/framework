<?php

class file_helper extends helper_core {
  private $image = null;

  public function __construct(){
	  App::import("image");
  }
  

  public function get_image($name, $record) {
	  $_POST[$name] = $this->save($name);
	  if(!empty($record->{$name})) {
		  $_info = App::model("files")->find("id", $record->{$name})->get_as_array();
		  $changed = false;
		  if(!empty($_POST[$name])) {
			  foreach($_POST[$name] as $_pk => $_pv) {
				  if($_info[$_pk] !== $_pv) {
					  $changed = true;
					  break;
				  }
			  }
			  if(!$changed) {
				  $_POST[$name] = $_info;
			  }
		  } else {
			  $_POST[$name] = $_info;
		  }
	  }
	  return $_POST[$name];
  }
  
  public function dataurl_to_file($filename, $dataurl, $upload) {
	  preg_match("/^data:image\/(jpeg|png|gif)/i", $dataurl, $match);
	  if(empty($match[1])) {
		  return false;
	  }
	  $dataurl = preg_replace("/^data:([^;]*);base64,/", "", $dataurl);
	  $encodedData = str_replace(' ', '+', $dataurl);
	  $decocedData = base64_decode($dataurl);
	  $ext = $match[1];
	  $filehash = hash("md5", uniqid($filename)) . "." . $ext;
	  $file = $upload . "img/" . $filehash;
	  file_put_contents($file, $decocedData);
	  $img = new image($file);
	  $_mime = mime_content_type($file);
	  $filesize = filesize($file);
	  $img->copy_image($upload . "thumbnail/" . $filehash);
	  $img->resize_image(200, 200);
	  return array(
		  "file" => $file,
		  "filename" => $filename,
		  "size" => $filesize,
		  "mime" => $_mime,
		  "path" => $filehash
	  );
  }
  
  public function fileupload($upload) {
	  $file = $_FILES["files"];
	  if(empty($file["tmp_name"])) {
		  return array(
			  "uploaded" => false,
			  "name" => $file["name"][0],
					   );
	  }
	  $img = new image($file["tmp_name"][0]);
	  $_mime = mime_content_type($file["tmp_name"][0]);
	  $filesize = filesize($file["tmp_name"][0]);
	  $dir = null;
	  if($img->check_image()){ 
		  $img->check_uploaded_image();
		  $uniqid = hash("md5", uniqid($file["name"][0]));
		  $filename = $uniqid . "." . $img->get_image_type();
		  $dir = "img";
		  $img->copy_image($upload . "img/" . $filename); 
		  $thumbnail = $filename;
		  $img->set_image($upload . "thumbnail/" . $thumbnail); 
		  $img->resize_image(100, 100); 
	  }elseif(isset($this->mime_table[$_mime])) { //画像以外
		  $ext = $this->mime_table[$_mime];
		  $uniqid = hash("md5", uniqid($file["name"][0]));
		  $filename = $uniqid . "." . $ext;
		  $dir = "mime";
		  move_uploaded_file($file["tmp_name"][0], $upload . "mime/" . $filename);
		  $thumbnail = $ext . ".png";
	  } else {
		  return array(
			  "uploaded" => false,
			  "name" => $file["name"][0],
					   );
	  }
	  return array(
		  "uploaded" => true,
		  "filename" => $filename,
		  "name" => $file["name"][0],
		  "size" => $filesize,
		  "type" => $_mime,
		  "dir"  => $dir,
		  "thumbnail" => $thumbnail
				   );
  }

  private function transfer($tmp, $upload, $_mime = null) {
	  $img = new image($tmp);
	  if($_mime === null) {
		  $_mime =  mime_content_type($tmp);
	  }
	  if($img->check_image()) {
		  $filename = uniqid() . "." . $img->get_image_type();
		  $path = $upload . "img/" . $filename;
		  $img->check_uploaded_image();
		  $img->set_image($path);
	  } elseif(in_array($_mime, $this->mime_table)) {
		  $filename = uniqid() . "." . $this->mime_table[$_mime];
		  $path = $upload . "mime/" . $filename;
		  move_uploaded_file($tmp, $path);
	  }
	  $link = config::fetch("static") . str_replace("./", "", $path);
	  return array($filename, $path, $link);
  }

  public function save($name){
	  if(!isset($_FILES[$name])) {
		  if(isset($_POST[$name])) {
			  return $_POST[$name];
		  }
		  return null;
	  }
	  $file = $_FILES[$name];
	  $upload = config::fetch("upload");
	  $res = array();
	  if(is_array($file["tmp_name"])) {
		  foreach($file["tmp_name"] as $key => $tmp) {
			  if(!isset($tmp[0])) {
				  continue;
			  }
			  $_res = array();
			  $_res["size"] = filesize($tmp);
			  $_res["mime"] = mime_content_type($tmp);
			  list($_res["filename"], $_res["path"], $_res["link"]) = $this->transfer($tmp, $upload);
			  $_res["file"] = $file["name"][$key];
			  $res[] = $_res;
		  }
	  } else {
		  $tmp = $file["tmp_name"];
		  if(isset($tmp[0])) {
			  $_res = array();
			  $_res["size"] = filesize($tmp);
			  $_res["mime"] = mime_content_type($tmp);
			  list($_res["filename"], $_res["path"], $_res["link"]) = $this->transfer($tmp, $upload);
			  $_res["file"] = $file["name"];
			  $res = $_res;
		  }
	  }
	  if(empty($res) && isset($_POST[$name])) {
		  return $_POST[$name];
	  } else {
		  return $res;
	  }
  }
  
  private $mime_table = array(
	  "application/pdf" => "pdf",
	  "application/zip" => "zip"
							  );
}