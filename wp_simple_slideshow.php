<?php
/*
Plugin Name: WP Simple Slideshow
Plugin URI: http://www.firstelement.jp/
Description: jQueryを使ったフォトスライドショー	。
Author: FirstElement
Version: 1.0
Author URI: http://www.firstelement.jp/
*/

/*  Copyright 2012 Takumi Kumagai (email : kumagai.t at firstelement.jp)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
load_plugin_textdomain( 'wp_simple_slideshow', false, basename(dirname(__FILE__)).DIRECTORY_SEPARATOR."languages" );
/****************************************************************************/
/*アクティベーション
/****************************************************************************/
register_activation_hook(__FILE__, 'wp_simple_slideshow_activate');
function wp_simple_slideshow_activate(){
	add_option('wp_simple_slideshow_style_height', 210);
	add_option('wp_simple_slideshow_style_width', 520);
	add_option('wp_simple_slideshow_left_distance', 20);
	add_option('wp_simple_slideshow_right_distance', -20);
	add_option('wp_simple_slideshow_duration_time', 1000);
	add_option('wp_simple_slideshow_wait_time', 7000);
	add_option('wp_simple_slideshow_image_reduction', 0);
	add_option('wp_simple_slideshow_image_list',array());
	scan_the_directory();
}
/****************************************************************************/
/*ファイルリストと、ディレクトリ内のファイルの辻褄を合わせる。
/****************************************************************************/
function scan_the_directory(){
	global $wpss_updir,$wpss_htmlpath;
	$list = array();
	
	if ($handle = @opendir($wpss_updir)) {
		while (false !== ($file = readdir($handle))) {
		$extension = pathinfo($file,PATHINFO_EXTENSION);
			if( ('jpg'==$extension) or ('jpeg'==$extension) or ('JPG'==$extension) or ('PNG'==$extension)){
				array_push($list,$wpss_htmlpath.$file);
			}
		}
		closedir($handle);
	}
	//var_export($list);
	update_option('wp_simple_slideshow_image_list',$list);
}
/****************************************************************************/
/*設定変数
/****************************************************************************/
include "uplord_pic.php";
include "delete_pic.php";
include "js.php";
include "style.php";

global $wpss_updir,$wpss_htmlpath;

$wpss_updir = wp_upload_dir();

$wpss_htmlpath = $wpss_updir['baseurl'].'/wp_simple_slideshow/';
$wpss_updir = $wpss_updir['basedir'].'/wp_simple_slideshow/';

$style_height = get_option('wp_simple_slideshow_style_height');
$style_width = get_option('wp_simple_slideshow_style_width');

$left_distance = get_option('wp_simple_slideshow_left_distance');
$right_distance = get_option('wp_simple_slideshow_right_distance');
$duration_time = get_option('wp_simple_slideshow_duration_time');
$wait_time = get_option('wp_simple_slideshow_wait_time');

$image_reduction = get_option('wp_simple_slideshow_image_reduction');
$image_url_list = get_option('wp_simple_slideshow_image_list');

/****************************************************************************/
/*ここまで
/****************************************************************************/
//フック
add_action('admin_menu', 'FlashPicture');
//ショートコード登録
add_shortcode('wp_simple_slideshow', 'echo_photgallery');

function wpss_jquery_loader(){wp_enqueue_script('jquery');}
add_action('wp_print_scripts','wpss_jquery_loader');
/****************************************************************************/
/*管理ページにメニューつける
/****************************************************************************/
function FlashPicture(){
	/*(ページタイトル, 付け加えるオプション名,ユーザーレベル, 実行ファイル,関数)*/
	add_menu_page('fe-photogallery', 'WP Simple Slideshow', 'administrator', __FILE__, 'get_flash_pictures');
	add_submenu_page(__FILE__, 'fe-photogallery', __('画像アップロード','wp_simple_slideshow'), 'administrator', 'uplord', 'flash_uplord_picture_management');
	add_submenu_page(__FILE__, 'fe-photogallery', __('画像削除','wp_simple_slideshow'), 'administrator', 'delete', 'flash_delete_picture_management');
}
/****************************************************************************/
/*管理ページ表示（選択画面）
/****************************************************************************/
function display_flash_pictures(){
	global $image_url_list;
?>
	<style>
		.toggle{
			font-size: 15px;
			font-weight: normal;
			line-height: 1;
			margin: 0;
			padding: 7px 10px;
			border-bottom: solid #E1E1E1 1px;
		}
		.inside{
			margin: 10px 0;
		}
	</style>
	<script>
		jQuery(function($){
			$(".inside:not(.main)").css("display","none");
			$(".toggle").click(function(){
				$(this).next(".inside").slideToggle("slow");
			});
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
		<div class="postbox">
			<h3 class="toggle">
				<?php _e('画像選択','wp_simple_slideshow');?>
			</h3>
			<div class="inside main thumbnail">
				<?php 
				$List_data = $image_url_list;
				if($List_data){
					echo "<p>".__('フォトギャラリーに表示させる画像を選択してください。','wp_simple_slideshow')."</p>";
					echo "<form method='post'>";
						$picture_xml_data = photgallery_load_slidexml();
						$i = 0;
						foreach($List_data as $img){
							$checkd = false;
							if(@in_array($img,$picture_xml_data)){
								$checkd = true;
							}
							
							print("<div style='width=25%;padding:5px;margin: 0 5px 5px 0; background-color: #f7f7f7;float:left;'><label>");
								print("<img src='" .$img."' />");
								print("<br />");
								
								if($checkd){
									print("<input type='checkbox' name='picture".$i ."' value ='OK' checked />&nbsp;".__('表示','wp_simple_slideshow')."</label>");
								}else{
									print("<input type='checkbox' name='picture".$i ."' value ='OK' />&nbsp;".__('表示','wp_simple_slideshow')."</label>");
								}
							print("</div>");	
							$i ++;
						}
						echo "<br style='clear:left;' />";
						echo "<input type='submit' name='action' value='".__('設定','wp_simple_slideshow')."' class='button-primary' />";
					echo "</form>";
				}else{
					echo "<p>".__('画像がアップロードされていません','wp_simple_slideshow')."<br />";
					echo '<a href="admin.php?page=uplord">'.__('画像アップロード','wp_simple_slideshow').'</a></p>';
				}
				?>
			</div>
		</div><!-- main -->
		
		<div class="postbox">
			<h3 class="toggle"><?php _e('プレビュー','wp_simple_slideshow');?></h2>
			<div class="inside main">
				<?php echo do_shortcode('[wp_simple_slideshow]');?>
			</div>
		</div><!-- preview -->
		
		<div class="postbox">
			<h3 class="toggle"><?php _e('コード','wp_simple_slideshow');?></h2>
			<div  class="inside">
				<form>
					<label>
						<?php _e('スライドショーを表示したい場所にショートコードを記載して下さい。','wp_simple_slideshow');?><br />
						<input type="text" value="[wp_simple_slideshow]" onclick="this.focus();this.select()">
					</label>
				</form>
			</div>
		</div><!-- code -->
		
		<div class="setting postbox">
			<h3 class="toggle"><?php _e('スライドショー設定','wp_simple_slideshow');?></h2>
			<div class="inside">
				<form method="post">
					<table border="0">
						<tr><td><?php _e('表示高さ','wp_simple_slideshow');?></td><td><input type="text" name="wp_simple_slideshow_style_height" size="5" value="<?php echo get_option('wp_simple_slideshow_style_height')?>">px</td>
							<td></td>
						</tr>
						<tr><td><?php _e('表示横幅','wp_simple_slideshow');?></td><td><input  type="text" name="wp_simple_slideshow_style_width" size="5" value="<?php echo get_option('wp_simple_slideshow_style_width')?>">px</td>
							<td></td>
						</tr>
						<tr><td><?php _e('奇数番目スライドイン量','wp_simple_slideshow');?></td><td><input  type="text" name="wp_simple_slideshow_left_distance" size="5" value="<?php echo get_option('wp_simple_slideshow_left_distance')?>">px</td>
							<td></td>
						</tr>
						<tr><td><?php _e('偶数番目スライドイン量','wp_simple_slideshow');?></td><td><input  type="text" name="wp_simple_slideshow_right_distance" size="5" value="<?php echo get_option('wp_simple_slideshow_right_distance')?>">px</td>
							<td></td>
						</tr>
						<tr><td><?php _e('切り替わり時間','wp_simple_slideshow');?></td><td><input  type="text" name="wp_simple_slideshow_duration_time" size="5" value="<?php echo get_option('wp_simple_slideshow_duration_time')?>"><?php _e('ミリ秒','wp_simple_slideshow');?></td>
							<td><?php _e('写真を切り替えるエフェクトに要する時間です。','wp_simple_slideshow');?></td>
						</tr>
						<tr><td><?php _e('切り替わり間隔','wp_simple_slideshow');?></td><td><input  type="text" name="wp_simple_slideshow_wait_time" size="5" value="<?php echo get_option('wp_simple_slideshow_wait_time')?>"><?php _e('ミリ秒','wp_simple_slideshow');?></td>
							<td><?php _e('次の写真に切り替わるまでの間隔の設定です。','wp_simple_slideshow');?></td>
						</tr>
						<p></p>
						<tr><td><?php _e('アップロード画像のリサイズ','wp_simple_slideshow');?></td><td><input type="text" name="wp_simple_slideshow_image_reduction" size="1" value="<?php echo get_option('wp_simple_slideshow_image_reduction')?>">（0=off/1=on）</td>
							<td><?php _e('有効にすると画像が上記で設定した大きさにトリミングされます。','wp_simple_slideshow');?><br /><?php _e('GDが入っている必要が有ります。','wp_simple_slideshow');?></td>
						</tr>
					</table>
					<input type="submit" name="option" value="<?php _e('設定','wp_simple_slideshow');?>" class="button-primary">
				</form>
			</div>
		</div>
	</div>
	
	<p><a href="http://forums.firstelement.jp/forum/wp-simple-slideshow"><?php _e('このプラグインに関するお問い合わせ','wp_simple_slideshow'); ?></a></p>
<?php
}
/****************************************************************************/
/*管理ページ表示（順番指定）
/****************************************************************************/
function display_flash_pictures_order(){
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
		<form method='post'>
			<h2><?php _e('フォトギャラリー','wp_simple_slideshow');?></h2>
			<p><?php _e('表示する順番を指定してください。','wp_simple_slideshow');?></p>
			<?php get_flash_order(); ?>
			<br style="clear:left;" />
			<input type='submit' name='action' value='<?php _e('設定','wp_simple_slideshow');?>' class="button-primary" />
			<input type='submit' name='action' value='<?php _e('戻る','wp_simple_slideshow');?>' class="button-secondary" />
			<input type='hidden' name='pic_order' value='ok' />
		</form>
	</div>
<?php
}
/****************************************************************************/
/*管理ページ表示（選択画面）
/****************************************************************************/
function display_flash_pictures_after(){
?>
	<div class="wrap">
		<form method='post'>
			<h2><?php _e('フォトギャラリー','wp_simple_slideshow');?></h2>
			<h3><?php _e('画像の選択は正常に終了しました。','wp_simple_slideshow');?></h3>
			<br style="clear:left;" />
			<input type='submit' name='action' value='<?php _e('戻る','wp_simple_slideshow');?>' class="button-secondary" />
		</form>
	</div>
<?php
}
/****************************************************************************/
/*POST分岐
/****************************************************************************/
function get_flash_pictures(){
	wp_enqueue_script('MyThumbnail',plugin_dir_url( __FILE__ ).'jquery.MyThumbnail.js',array('jquery'));
	
	if($_POST['action'] == __('設定','wp_simple_slideshow')){
		if($_POST['pic_order'] =="ok" && check_order() ==true)
			check_POST_data();
		else
			display_flash_pictures_order();
	}elseif($_POST['option'] == __('設定','wp_simple_slideshow')){
		photgallery_option();
	}else{
		display_flash_pictures();
	}
}
//****************************************************************************
//slide.xml読み込み
//****************************************************************************
function photgallery_load_slidexml(){
	global $wpss_updir;
	if(file_exists($wpss_updir."slides.xml")){
		$picture_xml_data = Array();
		$rawxml = simplexml_load_file($wpss_updir."slides.xml");
		if($rawxml == false){
			echo __('ファイルエラー','wp_simple_slideshow')."：slide.xml".__('が開けませんでした。','wp_simple_slideshow');
		}else{
			foreach($rawxml->slide as $picture_List){
				$picture_xml_data[] = $picture_List->jpegURL;	
		}	}
	}else{
		//echo "ファイルエラー：slides.xmlが存在しません。";
	}
	return $picture_xml_data;
}

/****************************************************************************/
/*確認ページ、順番指定
/****************************************************************************/
function get_flash_order(){
	global $image_url_list;

	$url_list= $image_url_list;

	for($i =0, $icnt =0; $i <count($url_list); $i++){
		$postkey ="picture".$i;
		if($_POST[$postkey] != ""){
			$pic_datas[$icnt][0] =str_replace("\n", "", $url_list[$i]);
			$pic_datas[$icnt][1] =$postkey;

			$icnt++;
		}
	}

	$pic_cnt =count($pic_datas);

	for($i =0; $i <$pic_cnt; $i++){
		print("<div style='width=25%;padding:5px;margin: 0 5px 5px 0; background-color: #f7f7f7;float:left;' class='thumbnail'><label>");
		print("<img src='" .$pic_datas[$i][0] ."' /><br />");

		print("<select name='pic_order" .$i ."'>");

		for($icnt =1; $icnt <($pic_cnt +1); $icnt++){
			if($_POST["pic_order" .$i] !=""){
				if($_POST["pic_order" .$i] ==$icnt)
					$selected =" selected ";
			}
			else{
				if($icnt == ($i+1))
					$selected =" selected ";
			}

			print("<option value='" .$icnt ."' " .$selected .">".$icnt);
			$selected ="";
		}
		print("</select>");
		_e('番目に表示する','wp_simple_slideshow');
		print("<input type='hidden' name='pic_url".$i ."' value='" .$pic_datas[$i][0] ."' />");
		print("<input type='hidden' name='" .$pic_datas[$i][1] ."' value='" .$_POST[$pic_datas[$i][1]] ."' />");
		print("</label>");
		//print("<br />コメント：<input type='hidden' name='coment".$i."' />");
		
		print("</div>");
	}
		print("<input type='hidden' name='maxpic' value='" .$i ."' />");
}
/****************************************************************************/
/*何が選ばれたかチェック(書き込みもする)
/****************************************************************************/
function check_POST_data(){
	global $wpss_updir;

	for($i =0; $i <$_POST['maxpic']; $i++){
		$url_list[$_POST["pic_order" .$i]] =$_POST["pic_url".$i];
		$coment[$i] = $_POST['coment'.$i];
	}

	for($i =0; $i <count($url_list); $i++){
		$url_list[$i] =$url_list[($i+1)];
	}


	$create_xml =fopen($wpss_updir ."slides.xml", "w");
	if($create_xml){
		flock($create_xml, LOCK_EX);
	
	//javascript用に編集
		fputs($create_xml, "<SlidesXML>\n");
		for($i =0; $i <count($url_list); $i++){
			if($url_list[$i] !=""){
				$url_list[$i] =str_replace("\n","",$url_list[$i]);
				fputs($create_xml, "  <slide>\n<jpegURL>" .$url_list[$i] ."</jpegURL>\n<numb>".$i ."</numb>\n"."<coment>".$coment[$i]."</coment>\n"."</slide>\n");
			}
		}
		fputs($create_xml, "</SlidesXML>\n");
		flock($create_xml, LOCK_UN);
		rewind($create_xml);
	}else{
		echo __('ファイルエラー','wp_simple_slideshow')."：slide.xml".__('が開けませんでした。','wp_simple_slideshow');
	}

	display_flash_pictures_after();
}
/****************************************************************************/
/*複数に同じ順番を設定してないか確認
/****************************************************************************/
function check_order(){
	for($i =0; $i <$_POST['maxpic']; $i++){
		$pic_order[$i] =$_POST["pic_order" .$i];
	}
	for($i =0; $i <count($pic_order); $i++){
		for($icnt =0; $icnt <count($pic_order); $icnt++){
			if($pic_order[$i] ==$pic_order[$icnt] && $i !=$icnt){
				print("<div class='wrap'>");
				print("<span style='color:#f00;'><h1>".__('順番','wp_simple_slideshow')."「 " .$pic_order[$i] ." 」".__('が複数あります。','wp_simple_slideshow')."</h1></span>");
				print("</div>");
				return false;
				break;
	}	}	}
	return true;
}
/****************************************************************************/
/*スライドショー設定書き込み
/****************************************************************************/
function photgallery_option(){
	$flag = true;
	echo __('表示高さ','wp_simple_slideshow').'：';
	if(intval($_POST['wp_simple_slideshow_style_height'])){
		update_option('wp_simple_slideshow_style_height',intval($_POST['wp_simple_slideshow_style_height']));
		_e('OK','wp_simple_slideshow');
	}else{
		$flag = false;
		_e('数字を1以上入力して下さい','wp_simple_slideshow');
	}
	
	echo '<br />'.__('表示幅','wp_simple_slideshow').'：';
	if(intval($_POST['wp_simple_slideshow_style_width'])){
		update_option('wp_simple_slideshow_style_width',intval($_POST['wp_simple_slideshow_style_width']));
		_e('OK','wp_simple_slideshow');
	}else{
		$flag = false;
		_e('数字を1以上入力して下さい','wp_simple_slideshow');
	}
	
	echo '<br />'.__('奇数番目スライドイン量').'：';
	if(is_int(intval($_POST['wp_simple_slideshow_left_distance']))){
		update_option('wp_simple_slideshow_left_distance',intval($_POST['wp_simple_slideshow_left_distance']));
		_e('OK','wp_simple_slideshow');
	}else{
		$flag = false;
		_e('数字を入力して下さい','wp_simple_slideshow');
	}
	
	echo '<br />'.__('偶数番目スライドイン量','wp_simple_slideshow').'：';
	if(is_int(intval($_POST['wp_simple_slideshow_right_distance']))){
		update_option('wp_simple_slideshow_right_distance',intval($_POST['wp_simple_slideshow_right_distance']));
		_e('OK','wp_simple_slideshow','wp_simple_slideshow');
	}else{
		$flag = false;
		_e('数字を入力して下さい','wp_simple_slideshow');
	}
	
	echo '<br />'.__('切り替わり時間','wp_simple_slideshow').'：';
	if(intval($_POST['wp_simple_slideshow_duration_time'])){
		update_option('wp_simple_slideshow_duration_time',intval($_POST['wp_simple_slideshow_duration_time']));
		_e('OK','wp_simple_slideshow');
	}else{
		$flag = false;
		_e('数字を1以上入力して下さい','wp_simple_slideshow');
	}
	
	echo '<br />'.__('切り替わり間隔').'：';
	if(intval($_POST['wp_simple_slideshow_wait_time'])){
		update_option('wp_simple_slideshow_wait_time',intval($_POST['wp_simple_slideshow_wait_time']));
		_e('OK','wp_simple_slideshow');
	}else{
		$flag = false;
		_e('数字を1以上入力して下さい','wp_simple_slideshow');
	}
	
	echo '<br />'.__('画像リサイズ').'：';
	if(is_int(intval($_POST['wp_simple_slideshow_image_reduction']))){
		update_option('wp_simple_slideshow_image_reduction',intval($_POST['wp_simple_slideshow_image_reduction']));
		_e('OK','wp_simple_slideshow');
	}else{
		$flag = false;
		_e('数字を入力して下さい','wp_simple_slideshow');
	}
	
	echo '<br />';
	if($flag){
		_e('設定完了しました','wp_simple_slideshow');
		echo '<br /><a href="admin.php?page=wp-simple-slideshow/wp_simple_slideshow.php">'.__('戻る','wp_simple_slideshow').'</a>';
	}else{
		_e('設定に失敗した項目が有ります！','wp_simple_slideshow');
	}
}
/****************************************************************************/
/*ショートコード
/****************************************************************************/
function echo_photgallery(){
	$return  = '<script type="text/javascript" src="'.plugins_url('jquery.easing.1.3.js',__FILE__).'"></script>';
	$return .= photgallery_css($style_height,$style_width);
	$return .= slideshowjs();
	$return .= '<div id="slideshow">'.__('スライドショー準備中','wp_simple_slideshow').'</div>';
	return $return;
}
?>