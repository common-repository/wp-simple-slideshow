<?php 
function photgallery_css(){
global $style_height,$style_width;
$height = $style_height.'px';
$width = $style_width.'px';
	$return = <<<END
	<style>
		#slideshow {
		    height: $height;
		    width: $width;
		    overflow: hidden;
		    position: relative;
		    padding: 0;
		    background-attachment: scroll;
		    background-clip: border-box;
		    background-color: transparent;
		    background-origin: padding-box;
		    background-position: left top;
		    background-repeat: no-repeat;
		    background-size: 100% auto;
		}
		#slideshow IMG {
		    left: 0;
		    opacity: 0;
		    position: absolute;
		    top: 0;
		    z-index: 8;
		    max-width: 100%;
		    width: 100%;
		    height: auto;
		    padding: 0;
		    margin: 0;
		    border: medium none;
		}
		#slideshow IMG.active {
		    opacity: 1;
		    z-index: 10;
		}
		#slideshow IMG.last-active {
		    z-index: 9;
		}
	</style>
END;
	return $return;
}?>