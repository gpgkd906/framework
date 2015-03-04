<?php
/**
 * image Class - このクラスは元画像をリサイズした画像を新たに生成し保存する。アップロード画像の単純保存も可能。
 * 
 * 画像を任意のサイズにリサイズし上書き保存する。
 * アップロード機能で使用する。
 *
 * [Hot To Use]
 * 		$img=new image($_FILES['name']['tmp_name'], array('jpeg','png','gif'), 800000); //フォーマット('jpeg','png','gif'), アップロードサイズの上限は800000byte
 *		if($img->check_image()){ //エラーが無ければ処理
 *			$img->check_uploaded_image(); //アップロードされたファイルかどうかのチェック
 *			$img->copy_image($fullfilename); //一時ファイルを指定した場所にコピー
 *			$img->resize_imge(300, 300);
 *			//copy→resizeを行い、最後にset→resize
 *			$img->set_image($fullfilename); //一時ファイルを指定した場所へ移動(/tmp内の画像はなくなる) ※最後に行う
 *			$img->resize_image(100, 100); //サイズを指定してリサイズ&保存
 *		}else{ //エラーの処理
 *			$e=$img->get_image_error(); //エラータイプを取得
 *			if($e === 'type'){
 *				$error_msg='対応している画像のフォーマットははjpg,png,gifのみです';
 *			}else if($e === 'size'){
 *				$error_msg='サイズは800KB以下のみです';
 *			}else if($e === 'upload'){
 *				$error_msg='アップロードされたファイルではありません'; //攻撃の可能性あり
 *			}
 *		}
 *
 * @author Kensuke
 * @date Last Modified  2010/05/09 (画像が劣化しないように内部に別のライブラリを利用)
 * 						2010/02/28 (全体的に改良)
 * 						2009/04/25
 *
 * Copyright(c) 2009-2010 InterOpera Inc.
 */

define('INCLUDE_LIB_PATH',dirname(__FILE__).'/lib/');
class image{
	
	
	/**
	 * プロパティを定義
	 *	$width  : 指定する横幅
	 * $height : 指定する縦幅
	 * $filename : ファイル名
	 * $ratio : 伸縮割合
	 * $extension : 拡張子
	 *
	 */
	protected $width=0;
	protected $height=0;
	protected $filename='';
	protected $ratio=0;
	protected $extension='';
	
	protected $flag='';
	protected $tmp_file='';
	protected $image_check=FALSE;
	protected $save_path='';
	protected $error=NULL;
	

	
	/**
	 * コンストラクタ
	 * @param String $tmp_path : アップロードされた画像のパス (Full Path)
	 * @param Array $allow_format : 許可する画像のフォーマット (デフォルトはJpeg, Png, Gif)
	 */
	public function __construct($tmp_path, $allow_format=array('jpeg','png','gif'), $allow_size=NULL){
		$this->tmp_path=$tmp_path;
		//画像のサイズ指定がある&サイズ以上の場合はエラー
		$size=getimagesize($this->tmp_path);
		if($allow_size !== NULL && $allow_size < filesize($this->tmp_path)){
			$this->image_check=FALSE;
			$this->error='size';
			return FALSE;
		}
		
		//画像のContent-typeをチェック。画像でなければエラー
		if(preg_match('/^image\/('.implode("|", $allow_format).')$/', $size['mime'], $ext)){
			$this->extension=$ext[1];
			$this->image_check=TRUE;
			
		}else{
			$this->image_check=FALSE;
			$this->error='type';
		}
	}
	
	/**
	 * ファイルがアップロードされたファイルかをチェックする(FALSEの場合はエラーに含める)
	 * @return bool : TRUE or FALSE
	 */
	public function check_uploaded_image(){
		if(is_uploaded_file($this->tmp_path)){
			return TRUE;
		}else{
			$this->error='upload';
			return FALSE;
		}
	}
	
	/**
	 * アップロードされた画像のContent-typeが正しいかどうかを返す
	 * @return TRUE / FALSE
	 */
	public function check_image(){
		if($this->image_check){
			return $this->image_check;
		}
	}
	
	
	/**
	 * エラーのタイプを取得する (ない場合はNULL)
	 * @return String $this->error : エラーのタイプ ('type', 'size', 'upload')
	 */
	public function get_image_error(){
		return $this->error;
	}
	

	/**
	 * tmpディレクトリから指定の場所&ファイル名に移動させる (/tmpディレクトリには元のアップロードファイルは残ってない, mv commandに相当)
	 * @param String $filename : 拡張子を含まないファイル名 (Full Path)
	 * @return unknown_type
	 */
	public function set_image($filename){
		$this->save_path=$filename;
		move_uploaded_file($this->tmp_path, $this->save_path);
	}
	/**
	 * /tmpディレクトリから指定の場所&ファイル名にコピーする(/tmpには元のアップロードファイルが残ったまま)
	 * @param String $filename : 拡張子を含まないファイル名 (Full Path)
	 * @return unknown_type
	 */
	public function copy_image($filename){
		$this->save_path=$filename;
		copy($this->tmp_path, $this->save_path);		
	}

	/**
	 * 画像の拡張子を取得
	 * @return String $this->extension : 画像の拡張子 (ドット「.」はつかない。 ex: 'jpeg')
	 */
	public function get_image_type(){
		return $this->extension;
	}


	/**
	 * 指定した画像をリサイズする
	 *
	 * $save_dirがある場合は、サムネイルとしてthumbnailディレクトリに保存される
	 * @param $size : リサイズするサイズ
	 * @param $save_dir : 保存するディレクトリパス,'thumbnail' or ''(デフォルトは'')
	 */	
	public function resize_image($w, $h){
		
		require_once INCLUDE_LIB_PATH.'ThumbLib.inc.php'; 
		$thumb = PhpThumbFactory::create($this->save_path);
		
		$thumb->resize($w, $h);
		
		$thumb->save($this->save_path);
		//$thumb->show();
	}

	/**
	 * 現在は使用していない old method
	 */
	public function __old_method(){
		/**
		 * 引数の$sizeをプロパティに代入
		 */
		$this->width=intval($size);
		$this->height=intval($size);


		/* ヘッダーのコンテントタイプ */
		//header('Content-type: image/'.$this->extension);


		/* Listにより、サイズを変数にする。 */
		list($old_width, $old_height) = getimagesize($this->save_path);

		/* 元画像のサイズを決められたサイズの大きさを合わせる */
		if($old_width >= $old_height){
			$ratio=$this->width/$old_width;
		}else{
			$ratio=$this->height/$old_height;
		}
		$new_width = $old_width * $ratio;
		$new_height = $old_height * $ratio;
		

		
		/**
		 * 拡張子によって、処理を分岐する。
		 * 
		 * ・画像を生成
		 * imagecreatetruecolor($width,$height) : Trueカラーを生成
		 * imagecreate($width,$height) : カラーを生成
		 *
		 * ・背景色を変更
		 * imagefill($image_id,0,0,0) : の場合は背景が黒になる
		 *
		 *
		 * ・コピーしてリサイズ
		 * imagecopysampled($new_image_id,$old_image_id,0,0,0,0,$new_width,$new_height,$old_width,$old_height)
		 *    :コピーを行う
		 * imagecopyresized($new_image_id,$old_image_id,0,0,0,0,$new_width,$new_height,$old_width,$old_height)
		 *    :imagecopyresampled()の方が滑らかにする(小さい画像には不向き)
		 *
		 * imagejpeg($new_image,$this->save_path) : 生成したイメージを保存
		 *
		 * imagedestroy($image_id) : メモリを開放
		 **/

		switch($this->extension){
		
			case 'jpeg':
			case 'jpg':
			
				$new_image = imagecreatetruecolor($new_width, $new_height);
				/* 背景が透明の場合、背景色を白にする */
				imagefill($new_image , 0 , 0 , 0xFFFFFF);
				
				$image = imagecreatefromjpeg($this->save_path);
				imagecopyresampled($new_image, $image, 0, 0, 0, 0, $new_width, $new_height, $old_width, $old_height);		
				imagejpeg($new_image,$this->save_path);
				
				/* メモリを開放 */
				imagedestroy($image);
				imagedestroy($new_image);
							
				break;
				
				
			case 'png':
				$new_image = imagecreatetruecolor($new_width, $new_height);
				/* 背景が透明の場合、背景色を白にする */
				imagefill($new_image , 0 , 0 , 0xFFFFFF);
				$image = imagecreatefrompng($this->save_path);

				imagecopyresampled($new_image, $image, 0, 0, 0, 0, $new_width, $new_height, $old_width, $old_height);		
				imagepng($new_image,$this->save_path);
				
				/* メモリを開放 */
				imagedestroy($image);
				imagedestroy($new_image);
											
				break;
				
				
			case 'gif':
				$new_image = imagecreatetruecolor($new_width, $new_height);
				/* 背景が透明の場合、背景色を白にする */
				imagefill($new_image , 0 , 0 , 0xFFFFFF);
				
				$image = imagecreatefromgif($this->save_path);
				imagecopyresampled($new_image, $image, 0, 0, 0, 0, $new_width, $new_height, $old_width, $old_height);		
				imagegif($new_image,$this->save_path);
				
				/* メモリを開放 */
				imagedestroy($image);
				imagedestroy($new_image);
							
										
				break;
		}
		

	}

/* -- End Resize Image Class -- */
}

?>
