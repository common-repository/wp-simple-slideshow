<?php
/****************************************************************************/
/*画像デリート関連
/****************************************************************************/
function flash_delete_picture_management(){
	wp_enqueue_script('MyThumbnail',plugin_dir_url( __FILE__ ).'jquery.MyThumbnail.js',array('jquery'));
?>
	<script>
		jQuery(function($){
			$(".thumbnail img").MyThumbnail({
			  thumbWidth:180,
			  thumbHeight:180
			});
		});
	</script>
	<div class="wrap">
		<div id="" class="clearfix">
			<div id="icon-options-general" class="icon32"></div>
			<h2>WP Simple Slideshow</h2>
		</div>
<?php
	if($_POST['action'] == __("消去",'wp_simple_slideshow'))
		flash_delete_picture_Confirmation();
	else if($_POST['action_really_delete'] == __("消去",'wp_simple_slideshow')){
		flash_delete_picture_processing();
		flash_delete_picture_form();
	}else{
		flash_delete_picture_form();
	}
 echo '</div>';
}
/****************************************************************************/
/*画像デリートフォーム
/****************************************************************************/
function flash_delete_picture_form(){
?>
	
		<FORM METHOD='post'>
			<h3><?php _e('サーバー上から消去する画像を選択してください。(現在フォトギャラリーに使用しているものは表示されません。)','wp_simple_slideshow');?></h3>
			<?php
				$list_file_url = get_option('wp_simple_slideshow_image_list');
				$picture_xml_data = photgallery_load_slidexml();
				
				for($i =0; $i <count($list_file_url); $i++){
				
					$checkd = false;
					if(in_array($list_file_url[$i],$picture_xml_data)){
						$checkd = true;	
					}
					
					if(!$checkd){
						echo "<div style='width=25%;padding:5px;margin: 0 5px 5px 0; background-color: #f7f7f7;float:left;' class='thumbnail'><label>";
						echo "<IMG SRC='" .$list_file_url[$i] ."'><br>";
						_e("この画像を消去",'wp_simple_slideshow');
						echo "<INPUT TYPE='CHECKBOX' NAME='picture".$i ."' VALUE ='".$list_file_url[$i]."'></label>";
						echo "</div>";
					}
				}
				print("<INPUT TYPE='hidden' name='maxpic' value='" .$i ."'>");
			?>
			<br style="clear:left;" />
			<input type='submit' name='action' value='<?php _e("消去",'wp_simple_slideshow');?>' class="button-primary" />
		</FORM>
	
<?php
}
/****************************************************************************/
/*チェックされたデータ確認
/****************************************************************************/
function flash_delete_picture_Confirmation(){
	
	$swicth = false;
	print("<FORM METHOD='post'>");
	print("<div class ='wrap'>");

	for($i =0; $i <$_POST['maxpic']; $i++){
		$delete_fileName =$_POST['picture' .$i];

		if($delete_fileName !=""){
			print("<img src='" .$delete_fileName ."' HEIGHT='125' WIDTH='125'/>　");
			print("<INPUT TYPE='hidden' name='picture" .$i ."' value='" .$delete_fileName ."'>");
			print( "<p>$delete_fileName</p>" );
			$swicth = true ;
		}
	}
	print("</div>");
	print("<div class ='wrap'>");
	
	if($swicth){
		_e("これらの画像を消去してよろしいすでか？",'wp_simple_slideshow');
		print("<input type='submit' name='action_really_delete' value='".__("消去",'wp_simple_slideshow')."' class='button' />　<input type='submit' name='action' value='".__('キャンセル','wp_simple_slideshow')."' class='button' />");
	}else{
		_e("何も選択されていませんでした",'wp_simple_slideshow');
		print("<input type='submit' name='action' value='".__('戻る','wp_simple_slideshow')." class='button' />");
	}
	print("<INPUT TYPE='hidden' name='maxpic' value='" .$_POST['maxpic'] ."'>");

	print("</div>");
	print("</FORM>");

}
/****************************************************************************/
/*チェックされたデータ消去
/****************************************************************************/
function flash_delete_picture_processing(){
	global $wpss_updir;
	$get_db_list = get_option('wp_simple_slideshow_image_list');

	for($i =0; $i <$_POST['maxpic']; $i++){
		$delete_fileName =$_POST['picture' .$i];

		$del_name =str_replace("//", "/", $delete_fileName);
		$del_name =explode("/", $del_name);

		$del_max =count($del_name) -1;

		$siteurllen =strlen(get_settings('siteurl'));
		$jpgpassdir =substr($list_file_httpurl, $siteurllen, strlen($list_file_httpurl));
		//$jpgpass =".." .$jpgpassdir .$del_name[$del_max];
		$jpgpass = $wpss_updir.$del_name[$del_max];

		if($delete_fileName !=""){
			if(file_exists($jpgpass)){
				unlink($jpgpass);
			}else{
				echo __('削除エラー','wp_simple_slideshow').$jpgpass;
				return;
			}
		}
		
		//ファイルリスト処理
		
		$array_kye = array_search($delete_fileName,$get_db_list);
		if(!($array_kye === false)){
			unset($get_db_list[$array_kye]);
		}
	}
	
	//配列を詰める
	$put_db_list = array();
	foreach($get_db_list as $val){
		array_push($put_db_list,$val);
	}
	update_option('wp_simple_slideshow_image_list',$put_db_list);
	print("<div class ='wrap'>");
	_e("選択された画像を消去しました。",'wp_simple_slideshow');
	print("</div>");
}
?>