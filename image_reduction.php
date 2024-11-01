<?php
function image_reduction($filename){
	global $style_height,$style_width;
	
	if(!function_exists('imagecreatetruecolor')){
		echo '<br>';
		_e('GDが有効では有りません','wp_simple_slideshow');
		return ;
	}
	
	$make_path = $filename;
	$create_img_width = $style_width;
	$create_img_height = $style_height;
	list($temp_image_width,$temp_image_height) = @getimagesize($filename);
	
	if( ( $temp_image_width / $temp_image_height ) > ( $create_img_width / $create_img_height ) ) { //横長の時
		$new_height = $create_img_height;
		$rate       = $new_height / $temp_image_height; //縦横比
		$new_width  = $rate * $temp_image_width;
		$x = ( $create_img_width - $new_width ) / 2;
		$y = 0;
		
	} else { //縦長の時
		$new_width  = $create_img_width;
		$rate       = $new_width / $temp_image_width; //縦横比
		$new_height = $rate * $temp_image_height;
		$x = 0;
		$y = ( $create_img_height - $new_height ) / 2;
	}
	
	$thumb = @ImageCreateTrueColor( $create_img_width, $create_img_height ); //空画像
	$source = @imagecreatefromjpeg($filename);
	
	if($thumb){
		imageCopyResampled($thumb, $source, $x, $y, 0, 0, $new_width, $new_height, $temp_image_width, $temp_image_height);
		imagejpeg($thumb,$make_path);
		imagedestroy($thumb);
	}else{
		echo "<br>";
		_e("画像を縮小できませんでした",'wp_simple_slideshow');
		return;
	}
	
	return true;
}
?>