<?php

################################################################################################
/**********************************************************************************************\
|**		Document:					image.functions.php                                      **|
|**		Creation:					April 10, 2012                                           **|
|**		Last modification:			May 24, 2012                                             **|
|**		Authors:					Abdoul aziz SENE                                         **|
|**		Project:					It's Now Baby, Industrial project 7, EMSE & LFKs		 **|
|**		Company:					EMSE, Cycle ISMIN, Promo 2013                            **|
|**		Description:				                                                         **|
\**********************************************************************************************/
################################################################################################



//define functions for image resizing 




function image_resize_and_save($width, $height, $directory, $destroy){
	global $nameDestination,$relative_link,$link, $id, $title,$extensionFile, $directoryDestination;
	
	//to load the image
	if ($extensionFile == 'jpg' OR $extensionFile == 'jpeg')
		$source = imagecreatefromjpeg($link);
	else if ($extensionFile == 'gd')	
		$source = imagecreatefromgd($link);
	else if ($extensionFile == 'png')	
		$source = imagecreatefrompng($link);
	else if ($extensionFile == 'gd2')
		$source = imagecreatefromgd2($link);
	else if ($extensionFile == 'gif')
		$source = imagecreatefromgif($link);
	else if ($extensionFile == 'wbmp')
		$source = imagecreatefromwbmp($link);
	else if ($extensionFile == 'xbm')
		$source = imagecreatefromxbm($link);
	else if ($extensionFile == 'xpm')
		$source = imagecreatefromxpm($link);
	else {
		$source = NULL ;
	}
	
	if(isset($source)){
		// the fonctions imagesx and imagesy returns the images dimensions
		$width_source = imagesx($source);
		$height_source = imagesy($source);
					
		//we have to resize only when $width and $height are positive
		if(($width>0) && ($height>0)){
		//figure out the new size of our image
			
			//we compare the width/height ratio to $width/$height
			if(($width_source/$height_source) > ($width/$height)){//means that the width is the overflow factor
				//then we check if the image is too large for our new size. In that case, we reduce the image with an alpha factor, else no touch
				if($width_source>$width)
					$alpha = $width/$width_source;
				else
					$alpha = 1;
			}
			else{//means that the height may be the overflow factor
				//then we check if the image is too long for our new size. In that case, we reduce the image with an alpha factor, else no touch
				if($height_source>$height)
					$alpha = $height/$height_source;
				else
					$alpha = 1;
			}
			
			//as we have our alpha factor, we can figure out the new size of our image
			$image_height = $height_source * $alpha ;
			$image_width = $width_source * $alpha ;
		}
		else{
			$image_height = $height_source;
			$image_width = $width_source;
		}
		
		//now we got the new size, we can start resizing
		// On create the image bloc
		if(!($destination = imagecreatetruecolor($image_width,$image_height))) // we just create an empty image
			return NULL;
			
		// To a white background
		$white = ImageColorAllocate($destination, 255, 255, 255);  
		ImageFillToBorder($destination, 0, 0, $white, $white); 

		// the resize
		if(!imagecopyresampled($destination, $source, 0, 0, 0, 0, $image_width, $image_height, $width_source, $height_source))
			return NULL;
	
	
		//then we can save the image
		$nameDestination = $id."_".$title.".".$image_width."x".$image_height.".png";
		$relative_link = $directory.$nameDestination ;
		//image saved as a png
		$save = imagepng($destination, $directoryDestination.$relative_link);
		
		if(!($save)){
			// destruction of temporary images
			imagedestroy($source);  
			imagedestroy($destination);
			return NULL;
		}
		else{
			if($destroy){//if we can destroy the original uploaded file
				unlink($link);
				$link = $directoryDestination.$relative_link;
				$extensionFile = "png";
			}
			// destruction of temporary images
			imagedestroy($source);  
			imagedestroy($destination);
			return 1;
		}
	}
	else return NULL;
}

