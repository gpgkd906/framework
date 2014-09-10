<?php

class image_helper extends helper_core {
  private $image = null;

  public function __construct(){
	  App::import("image");
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

  public function save_file($files = null){
	  trigger_error("image_helper::bad method, Stop!!!", E_USER_ERROR);
	  die();
	  $post_injection = false;
	  if(empty($files)) {
		  $files = $_FILES;
		  $post_injection = true;
	  }
	  $upload = config::fetch("upload");
	  $res = array();
	  $uploaded = array();
	  foreach($files as $name => $file) {
		  if(!$file["tmp_name"]) {
			  continue;
		  }
		  $img = new image($file["tmp_name"]);
		  $_mime = mime_content_type($file["tmp_name"]);
		  if($img->check_image()) {
			  $uploaded["name"] = $name;
			  $uploaded["filename"] = $filename = "img/" . uniqid($name) . "." . $img->get_image_type();
			  $uploaded["size"] = filesize($file["tmp_name"]);
			  $img->check_uploaded_image();
			  $img->set_image($upload . $filename);
			  $res[$name] = $filename;
			  $mime_type[$name] = $_mime;
		  } elseif(in_array($_mime, $this->mime_table)) {
			  $filename = "mime/" . uniqid($name) . "." . $this->mime_table[$_mime];
			  $filesize[$name] = filesize($file["tmp_name"]);
			  move_uploaded_file($file["tmp_name"], $upload . $filename);
			  $res[$name] = $filename;
			  $mime_type[$name] = $mime;
			  
		  }		  
	  }
	  if($post_injection) {
		  $_POST = array_merge($_POST, $res);
		  $_POST["helper_mime_type"] = $mime_type;
		  $_REQUEST = array_merge($_REQUEST, $res);
		  $_REQUEST["helper_mime_type"] = $mime_type;
	  }
	  return $res;
  }
  
  private $mime_table = array(
	  "application/pdf" => "pdf",
	  "application/zip" => "zip"
							  );
}