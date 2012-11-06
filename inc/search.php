<?php 


################################################################################################
/**********************************************************************************************\
|**		Document:					search.php                                               **|
|**		Creation:					May 28, 2012                                             **|
|**		Last modification:			June 27, 2012                                            **|
|**		Authors:					Abdoul aziz SENE                                         **|
|**		Project:					It's Now Baby, Industrial project 7, EMSE & LFKs		 **|
|**		Company:					EMSE, Cycle ISMIN, Promo 2013                            **|
|**		Description:				returns the results of a search                                                  **|
\**********************************************************************************************/
################################################################################################



// CONNECT to DB
include("config.php");
include("connexionbdd.php");
Connexion_BDD();

if(!isset($_GET['val'])) 
	$message = "
		<div class='wrapper' >
			<div class='grid_3' >&nbsp;</div>
			<div class='grid_6' >
				<div class='info-box' style='width:350px;border-radius:10px;' >
					<p class='icon'><img src='images/icon-info.png' ></p>
					An error occured while seacrh proscessing.
				</div>
			</div>
		</div>";
else{
	extract($_GET);
}
 
function highlight($expr){
	global $val;
	return str_ireplace($val, "<span style='background:url(images/input_color1.png);' >".substr($expr, stripos($expr, $val), strlen($val)).'</span>', $expr);
}

?>
<script charset='utf8' type='text/javascript' >

	
	function stripos (f_haystack, f_needle, f_offset) {//js adaptation of php function stripos
		var haystack = (f_haystack + '').toLowerCase();
		var needle = (f_needle + '').toLowerCase();
		var index = 0;
	 
		if ((index = haystack.indexOf(needle, f_offset)) !== -1) {
			return index;
		}
		return false;
	}

			
	var val = "<?php echo $val; ?>";
	
	function js_highlight(expr){
		var expression = expr,
			go_on = true,
			tag = "<span style='background:url(images/input_color1.png);' >",
			close_tag = "</span>",
			offset = 0;
		while(go_on){
			var begin = stripos(expression, val, offset);
			if(begin == false)
				go_on = false;
			else{
				expression = text_visible.substr(0, begin) + tag + expression.substr(begin,val.length) + close_tag + expression.substr(begin + val.length);
				offset = begin + val.length ;
			}	
		}
		return expression;
	}
	
	
</script>
<?php


//a function to display weights 
function format_bytes($a_bytes)
{
    if ($a_bytes < 1024) {
        return $a_bytes .' B';
    } elseif ($a_bytes < 1048576) {
        return round($a_bytes / 1024, 2) .' KB';
    } elseif ($a_bytes < 1073741824) {
        return round($a_bytes / 1048576, 2) . ' MB';
    } elseif ($a_bytes < 1099511627776) {
        return round($a_bytes / 1073741824, 2) . ' GB';
    } elseif ($a_bytes < 1125899906842624) {
        return round($a_bytes / 1099511627776, 2) .' TB';
    } elseif ($a_bytes < 1152921504606846976) {
        return round($a_bytes / 1125899906842624, 2) .' PB';
    } elseif ($a_bytes < 1180591620717411303424) {
        return round($a_bytes / 1152921504606846976, 2) .' EB';
    } elseif ($a_bytes < 1208925819614629174706176) {
        return round($a_bytes / 1180591620717411303424, 2) .' ZB';
    } else {
        return round($a_bytes / 1208925819614629174706176, 2) .' YB';
    }
}

$player_or_reader = ''; $details = ''; $images_path = '';$images_path_original = '';
switch($_field){
	case 1/*archive*/: $field = 'archive'; break;
	case 2/*image*/: $field = 'image'; break;
	case 3/*audio*/: $field = 'audio'; break;
	case 4/*video*/: $field = 'video'; break;
	default: $field = 'text'; break;
}
		
$limit_inf = 30*($page - 1) ;
$limit_sup = 30*$page ;


//we get the list of medias
	$query = "SELECT * FROM inb_medias_1 WHERE mediatype=".$mediatype." AND link_type=".$linktype." AND 
		   (title LIKE '%".$val."%' OR 
			authors LIKE '%".$val."%' OR 
			text LIKE '%".$val."%' OR 
			themes LIKE '%".$val."%' OR 
			locations LIKE '%".$val."%' OR 
			persons LIKE '%".$val."%' OR 
			genre LIKE '%".$val."%' OR 
			misc LIKE '%".$val."%' OR 
			key_areas LIKE '%".$val."%' OR 
			subtitles LIKE '%".$val."%' ) ORDER BY title LIMIT ".$limit_inf.",".$limit_sup;
	$request = mysql_query($query);
	if(!($request))
	{
		echo "An error has occurred during the information gathering (level 0) from the database: <br />".mysql_error()."<br /> ";
		exit();
	}
	
	if($_field == 0/*text*/) {//texts are different from other
		if(mysql_num_rows($request) <= 0) {
			echo "
				<div class='wrapper' >
					<div class='grid_3' >&nbsp;</div>
					<div class='grid_6' >
						<div class='info-box' style='width:350px;border-radius:10px;' >
							<p class='icon'><img src='images/icon-info.png' ></p>
							No result for this value : ".$val."
						</div>
					</div>
				</div>"; //the message if the list is empty
		} else {
			while($tab = mysql_fetch_assoc($request)){ //for each text, we display 
				$extract_of = '';//we need the title of the archive which the text is extracted from
				if($tab['extract_of'] != 0){
					$query_in = sprintf("select title from inb_medias_1 WHERE id='%d'", $tab['extract_of']);
					$request_in = mysql_query($query_in);
					$extract_of = mysql_result($request_in, 0);
				}
				$associated_audio = '';//we need the title of the audio track which the text is associated
				if($tab['associated_audio'] != 0){
					$query_in = sprintf("select title from inb_medias_1 WHERE id='%d'", $tab['associated_audio']);
					$request_in = mysql_query($query_in);
					$associated_audio = mysql_result($request_in, 0);
				}
				$associated_video = '';//we need the title of the video which the text is associated
				if($tab['associated_video'] != 0){
					$query_in = sprintf("select title from inb_medias_1 WHERE id='%d'", $tab['associated_video']);
					$request_in = mysql_query($query_in);
					$associated_video = mysql_result($request_in, 0);
				}
				$player_or_reader = "";//no player
				?>
				<div id="all_text_<?php echo $tab['id']; ?>" >
					
					<div class="wrapper" >
						<div class='grid_11' style="<?php echo $public ? "" : "width:640px;" ; ?>background: url('images/input.png'); margin:1px; font-size:1.3em; border-radius:5px;cursor:pointer;" >
							<div class='grid_10 one_title' style="border-radius:15px;<?php echo $public ? "" : "width:520px;" ; ?>padding:5px;" onclick="if(text_<?php echo $tab['id']; ?>) {$('#text_<?php echo $tab['id']; ?>').slideUp('slow'); text_<?php echo $tab['id']; ?> = false;} else { $('#text_<?php echo $tab['id']; ?>').slideDown('slow'); text_<?php echo $tab['id']; ?> = true;}" >
								<span class="normaltip" 
									title="
										<b><?php echo $public ? "Extrait de" : "Extract of" ; ?></b> : <?php echo $extract_of; ?><br>
										<b><?php echo $public ? "Audio associé" : "Associated audio" ; ?></b> : <?php echo $associated_audio; ?><br>
										<b><?php echo $public ? "Vidéo associée" : "Associated video" ; ?></b> : <?php echo $associated_video; ?><br>
										<b><?php echo $public ? "Thèmes" : "Themes" ; ?></b> : <span style='color:#ff3333;font-weight:bold;' ><?php echo highlight($tab['themes']); ?></span><br>
										<b><?php echo $public ? "Lieux" : "Locations" ; ?></b> : <span style='color:#ff33cc;font-weight:bold;' ><?php echo highlight($tab['locations']); ?></span><br>
										<b><?php echo $public ? "Personnes" : "Persons" ; ?></b> : <span style='color:#5555ff;font-weight:bold;' ><?php echo highlight($tab['persons']); ?></span><br>
										<b><?php echo $public ? "Type" : "Genre" ; ?></b> : <span style='color:#3399ff;font-weight:bold;' ><?php echo highlight($tab['genre']); ?></span><br>
										<b><?php echo $public ? "Autres" : "Misc" ; ?></b> : <span style='color:#33ff33;font-weight:bold;' ><?php echo highlight($tab['misc']); ?></span><br>
										<?php if(!$public){ ?>
										<b>System</b> : <span style='color:#ccff33;font-weight:bold;' ><?php echo $tab['system']; ?></span><br>
										<b>Linked to</b> : <span style='color:#ff9933;font-weight:bold;' ><?php echo $tab['direct_links']; ?></span><br>
										<?php } ?>
										"  >
									<?php echo highlight($tab['title']); ?>
								</span>
							</div>	
							<?php if(!$public){ ?>
							<div class="grid_2" style="text-align:center;width:60px;" >
								<a href='text_add.php?id=<?php echo $tab['id']; ?>' ><img src='images/edit15.png' class="normaltip" title='Edit' style='position:relative; top:6px;' /></a>&nbsp;
								<img src='images/del15.png' class="normaltip" title='Delete' style='position:relative; top:6px;' onclick="delete_media(<?php echo $tab['id'].",'all_text_".$tab['id']."','','text'"; ?>)" />
							</div>
						</div>
						<div class="grid_1" style='text-align:center;padding:5px;background:url("images/input.png"); font-size:1.3em; border-radius:5px;cursor:pointer;' ><?php echo $tab['id']; ?></div>
						<div class="grid_1" style='text-align:center;padding:5px;background:url("images/input.png"); font-size:1.3em; border-radius:5px;cursor:pointer;' ><?php if($tab['previous']>0) echo $tab['previous']; else echo "&nbsp;"; ?> </div>
						<div class="grid_1" style='text-align:center;padding:5px;background:url("images/input.png"); font-size:1.3em; border-radius:5px;cursor:pointer;' > <?php if($tab['next']>0) echo $tab['next']; else echo "&nbsp;"; ?></div>
					</div>
							<?php } else { ?>
						</div>
					</div>
					<?php } ?>
					<div class="wrapper" id="text_<?php echo $tab['id']; ?>" style="display:none;" >
						<div class="grid_1" >&nbsp;</div>
						<div class="grid_9" style="text-align:justify;" >
							<p id="text__<?php echo $tab['id']; ?>" ><?php echo html_entity_decode($tab['text']); ?></p>
						</div>
					</div>
				</div>
				<script type="text/javascript" charset="utf8" > 
					//here the script which colors the key areas and adds tips
					text_visible = "<?php echo html_entity_decode($tab['text']);?>";//here the reason why we can't admit carriage return, it would make this line wrong, same for the application
					var text_<?php echo $tab['id']; ?> = false;
					if(<?php if ($tab['key_areas'] != "") echo 'true'; else echo 'false'; ?>){
						tab_of_areas = explode('|','<?php echo $tab['key_areas']; ?>');
						shift_tab = Array();
						count = 0;
						shift = 0;
						for(var i in tab_of_areas)
						{
							one_area =explode("~",tab_of_areas[i]);
							tag = "<span style='font-weight:bold;color:#5555ff' class='normaltip' title=\"<b>Themes</b> :<span style='color:#ff3333;font-weight:bold;' >"+one_area[2]+"</span><br><b>Locations</b> : <span style='color:#ff33cc;font-weight:bold;' >"+one_area[3]+"</span><br><b>Persons</b> : <span style='color:#5555ff;font-weight:bold;' >"+one_area[4]+"</span><br><b>Genre</b> : <span style='color:#3399ff;font-weight:bold;' >"+one_area[5]+"</span><br><b>Misc</b> : <span style='color:#33ff33;font-weight:bold;' >"+one_area[6]+"</span><br><b>System</b> : <span style='color:#ccff33;font-weight:bold;' >"+one_area[7]+"</span><br><b>Linked to</b> : <span style='color:#ff9933;font-weight:bold;' >"+one_area[8]+"</span>\" >";
							shift = shift_fct(parseInt(one_area[0]),shift_tab);
							shift_tab[count] = new Array(parseInt(one_area[0]),tag.length + close_tag.length);
							text_visible = text_visible.substr(0, parseInt(one_area[0])+shift) + tag + text_visible.substr(parseInt(one_area[0])+shift,parseInt(one_area[1])-parseInt(one_area[0])) + close_tag + text_visible.substr(parseInt(one_area[1])+shift);
							$("#text__<?php echo $tab['id']; ?>").html(text_visible);
							count++ ;
						}
						$("#text__<?php echo $tab['id']; ?>").html(js_highlight(text_visible));
					}
					else   	
						$("#text__<?php echo $tab['id']; ?>").html(js_highlight(text_visible));
				</script>
				
				<?php 
			} 
		}
	}
	else{
		if(mysql_num_rows($request) <= 0) {
			echo "
				<div class='wrapper' >
					<div class='grid_3' >&nbsp;</div>
					<div class='grid_6' >
						<div class='info-box' style='width:350px;border-radius:10px;' >
							<p class='icon'><img src='images/icon-info.png' ></p>
							No result for this value : ".$val."
						</div>
					</div>
				</div>"; //the message if the list is empty
		} else {
			while($tab = mysql_fetch_assoc($request)){
				//we will different players or readers for each type of media
				$associated_text = '';//we need the title of the text which the media is associated
				if($tab['associated_text'] != 0){
					$query_in = sprintf("select title from inb_medias_1 WHERE id='%d'", $tab['associated_text']);
					$request_in = mysql_query($query_in);
					$associated_text = mysql_result($request_in, 0);
				}
				switch($_field){
					case 1/*archive*/:
						$player_or_reader = "<object data=\\\"".$tab['link']."\\\" type=\\\"application/pdf\\\" height=\\\"400\\\" width=\\\"750\\\" ></object>"; 
						$details = "
							<b>Weight </b>: ".format_bytes($tab['weight'])." <br>
							";
						break;
					
					case 2/*image*/: 
						$images_path = "medias/images/original/";
						$images_path_original = "medias/images/original/";
						$size = getimagesize("../".$images_path.$tab['link']);
						// the image size isn't fixed, so for each image, we set up the values of height and width attibutes of img tag
						//we first compare the width/height ration to 750/MEDIAS_LIST_DISPLAY_HEIGHT
						if(($size[0]/$size[1]) > (MEDIAS_LIST_DISPLAY_WIDTH/MEDIAS_LIST_DISPLAY_HEIGHT)){//means that the width is the overflow factor
							//then we check if the image is too large for our div tag. In that case, we reduce the image with an alpha factor
							if($size[0]>MEDIAS_LIST_DISPLAY_WIDTH)
								$alpha = MEDIAS_LIST_DISPLAY_WIDTH/$tab['width'];
							else
								$alpha = 1;
						}
						else{//means that the height may be the overflow factor
							//then we check if the image is too large for our div tag. In that case, we reduce the image with an alpha factor
							if($size[1]>MEDIAS_LIST_DISPLAY_HEIGHT)
								$alpha = MEDIAS_LIST_DISPLAY_HEIGHT/$tab['height'];
							else
								$alpha = 1;
						}
						//as we have our alpha factor, we can figure out the new size of our image
						$image_height = $size[1] * $alpha ;
						$image_width = $size[0] * $alpha ;
						//then we display our image		
						$player_or_reader = "<div style=\\\"height=".$image_height."px;width=750px;text-align:center;\\\" ><a href=\\\"".$images_path_original.$tab['link']."\\\" target=\\\"blank\\\" ><img src=\\\"".$images_path_original.$tab['link']."\\\" height=\\\"".$image_height."\\\" width=\\\"".$image_width."\\\" /></a></div>";  //no carriage return 
						$details = "
							<b>Weight </b>: ".format_bytes($tab['weight'])." <br>
							<b>Size </b>: ".$size[0]."x".$size[1]." <br>
							";
						break;
						
					case 3/*audio*/:
						$associated = "<b>Associated text</b> : ".$associated_text." <br>";
						//audio player
						$player_or_reader = "<audio src=\\\"".$tab['link']."\\\" height=\\\"270\\\" id=\\\"audio".$tab['id']."\\\" width=\\\"480\\\"></audio>"; //no carriage return
						$details = "
							<b>Weight </b>: ".format_bytes($tab['weight'])." <br>
							<b>Length </b>: ".$tab['length']." sec<br>
							";
						break;

					
					case 4/*video*/: 
						$associated = "<b>Associated text</b> : ".$associated_text." <br>";
						//video player
						$player_or_reader = "<video src=\\\"".$tab['link']."\\\" height=\\\"270\\\" id=\\\"video".$tab['id']."\\\" width=\\\"480\\\"></video>";  //no carriage return
						$details = "
							<b>Weight </b>: ".format_bytes($tab['weight'])." <br>
							<b>Size </b>: ".$tab['width']."x".$tab['height']." <br>
							<b>Length </b>: ".$tab['length']." <br>
							";
						break;
						
					default: $player_or_reader = ""; $details = ""; break;
				}
				?>
				<script type="text/javascript" charset="utf8" >
					var <?php echo $field.'_'.$tab['id']; ?> = false;
				</script>
				<?php 
				if($_field == 3) { ?>
				<script type="text/javascript" charset="utf8" >
					var sub_<?php echo $tab['id']; ?> = false;
					sub_type[<?php echo $tab['id']; ?>] = [];
					sub_type[<?php echo $tab['id']; ?>][1] = 0;
				</script>
				<?php } ?>					
				<div id="<?php echo "all_".$field."_".$tab['id']; ?>" >
					<div class="wrapper" >
						<div class="grid_10" style="<?php echo $public ? "" : "width:730px;" ; ?>background: url('images/input.png'); margin:1px; font-size:1.3em; height:30px; border-radius:5px;cursor:pointer;" >
							<div class="grid_9 one_title" style="border-radius:15px;<?php echo $public ? "" : "width:580px;" ; ?>padding:5px;" onclick='if(<?php echo $field.'_'.$tab['id']; ?>) {hide_media("<?php echo $field."\",".$tab['id']; ?>); <?php echo $field.'_'.$tab['id']; ?> = false;} else { show_media("<?php echo $field."\",".$tab['id'].",\"".$player_or_reader; ?>"); <?php echo $field.'_'.$tab['id']; ?> = true;}' >
								<span class="normaltip" title="<div cherset='utf8' >
										<?php echo $associated; ?>
										<b><?php echo $public ? "Thèmes" : "Themes" ; ?></b> : <span style='color:#ff3333;font-weight:bold;' ><?php echo highlight($tab['themes']); ?></span><br>
										<b><?php echo $public ? "Lieux" : "Locations" ; ?></b> : <span style='color:#ff33cc;font-weight:bold;' ><?php echo highlight($tab['locations']); ?></span><br>
										<b><?php echo $public ? "Personnes" : "Persons" ; ?></b> : <span style='color:#6633ff;font-weight:bold;' ><?php echo highlight($tab['persons']); ?></span><br>
										<b><?php echo $public ? "Type" : "Genre" ; ?></b> : <span style='color:#3399ff;font-weight:bold;' ><?php echo highlight($tab['genre']); ?></span><br>
										<b><?php echo $public ? "Autres" : "Misc" ; ?></b> : <span style='color:#33ff33;font-weight:bold;' ><?php echo highlight($tab['misc']); ?></span><br>
										<?php if(!$public){ ?>
										<b>System</b> : <span style='color:#ccff33;font-weight:bold;' ><?php echo $tab['system']; ?></span><br>
										<b>Linked to</b> : <span style='color:#ff9933;font-weight:bold;' ><?php echo $tab['direct_links']; ?></span><br>
										<?php } ?>
										</div>"  >
										<?php echo highlight($tab['title']); ?>
								</span>
							</div>
							<?php if(!$public){ ?>
							<div class="grid_2" style="text-align:center;width:90px;" >
								<a href='<?php echo $field; ?>_add.php?id=<?php echo $tab['id']; ?>' ><img src='images/edit15.png' class="normaltip" title='Edit' style='position:relative; top:6px;' /></a>&nbsp;
								<?php if($_field == 3) { ?>
								<img src='images/sub15.png' class="normaltip" title='Subtitles' style='position:relative; top:6px;' onclick="if(<?php echo 'sub_'.$tab['id']; ?>) {$('#<?php echo 'sub_'.$tab['id']; ?>').slideUp('slow'); <?php echo 'sub_'.$tab['id']; ?> = false;} else { $('#<?php echo 'sub_'.$tab['id']; ?>').slideDown('slow'); <?php echo 'sub_'.$tab['id']; ?> = true;}" />
								<?php } ?>	
								<img src='images/del15.png' class="normaltip" title='Delete' style='position:relative; top:6px;' onclick="delete_media(<?php echo $tab['id'].",'all_".$field."_".$tab['id']."','".urlencode($tab['link'])."','".$field."'"; ?>)" />
							</div>
						</div>
						<div class="grid_1" style='text-align:center;padding:5px;background: url(images/input.png);border-radius:5px;cursor:pointer;' ><?php echo $tab['id']; ?></div>
						<div class="grid_1 normaltip" style='text-align:center;padding:5px;background: url(images/input.png);border-radius:5px;cursor:pointer;' title="<?php echo $details; ?>" ><?php echo $tab['extension']; ?></div>
					</div>
							<?php } else { ?>
						</div>
						<div class="grid_1 normaltip" style='text-align:center;padding:5px;background: url(images/input.png);border-radius:5px;cursor:pointer;' title="<?php echo $details; ?>" ><?php echo $tab['extension']; ?></div>
					</div>
					<?php } ?>
					<div class="wrapper" id="<?php echo $field."_".$tab['id']; ?>" style="display:none;" ></div>
					<?php if($_field == 3) { ?>
					<div class="wrapper" id="<?php echo "sub_".$tab['id']; ?>" style="display:none;" >
						<script type="text/javascript" charset="utf8" >
							var count_<?php echo $tab['id']; ?> = 1;
							var length_<?php echo $tab['id']; ?> = <?php echo $tab['length']; ?>;
						</script>
						<div id='subtitles_<?php echo $tab['id']; ?>' >
							<div class='wrapper' style='text-align:center;font-weight:bold;height:30px;padding:auto;' >
								<div class='grid_1' > Line </div> 
								<div class='grid_1' > From </div> 
								<div class='grid_1' > To (sec) </div> 
								<div class='grid_6' > Subtitle </div> 
							</div>
							<div class='wrapper' id='<?php echo $tab['id']; ?>_wrapper_1' >
								<div class='grid_1' style='background:url(images/input.png) 0 0 repeat;text-align:center;font-size:1.3em;height:20px;margin:1px;padding-top:6px;border-radius:5px;' > 1 </div> 
								<div class='grid_1' > <input disabled type='text' id='<?php echo $tab['id']; ?>_begin_1' value='0' style='width:50px;' /> </div> 
								<div class='grid_1' > <input type='text' id='<?php echo $tab['id']; ?>_end_1'  style='width:50px;' /> </div> 
								<div class='grid_6' > <input type='text' id='<?php echo $tab['id']; ?>_subtitle_1' style='width:460px;'  /> </div> 
							</div>
						</div><br>
						<script type="text/javascript" charset="utf8" >
							count_<?php echo $tab['id']; ?> = load_subtitles(<?php echo $tab['id']; ?>,'<?php echo $tab['subtitles']; ?>');
						</script>
					</div>
					<?php } ?>	
				</div>
				<?php 
			} 	
		}	
	
	}
?>
 