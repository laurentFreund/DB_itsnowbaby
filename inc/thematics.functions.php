<?php

################################################################################################
/**********************************************************************************************\
|**		Document:					thematics.functions.php                                  **|
|**		Creation:					March 21, 2012                                           **|
|**		Last modification:			June 27, 2012                                            **|
|**		Authors:					Abdoul aziz SENE                                         **|
|**		Project:					It's Now Baby, Industrial project 7, EMSE & LFKs		 **|
|**		Company:					EMSE, Cycle ISMIN, Promo 2013                            **|
|**		Description:				some recurrent functions used for thematics              **|
\**********************************************************************************************/
################################################################################################



//for one field (ex: themes), this function returns all the options in the right tags
function get_select_options($table,$category,$order,$except){
	global $error,$errorText ;
	
	$query_except = "";
	if($except != '') $query_except = "WHERE id<>".$except;//for direct links, sometimes an execption mustn't be displayed
	$cat = '';
	if($table == 'inb_keywords') $cat = "WHERE category = '".$category."'";
	
	$init="<option value=0 >  ---- none ----  </option>";
	if($category == '5'){//no init for direct links
		$init="";
	}
	
	//request options
	$query = sprintf("select * from %s %s %s ORDER by %s", $table, $cat, $query_except, $order );
	$request = mysql_query($query);
	
	$options = '';
	$t = '';
	$s = '';
	
	if($request){
		while($tab = mysql_fetch_assoc($request)){//create the list in option tags
			if($table == 'inb_medias_1'){
				switch($tab['mediatype']){//In the direct links field, the medias' titles will have a suffixe to indicate the media type 
					case MEDIATYPE_TEXT: ($tab['link_type'] == LINK_TYPE_NO_LINK)? $m = "(Txt)" : $m = "(Pdf)" ; break;
					case MEDIATYPE_AUDIO: $m = "(Au)" ; break;
					case MEDIATYPE_VIDEO: $m = "(Vid)" ; break;
					case MEDIATYPE_IMAGE: $m = "(Im)" ; break;
					default: $m = "(Unk)" ; break;
				}
			}else $m = '';
			
			//the default value of system is 'for application'
			if(strlen($tab[$order])>35) $t = "title='".$tab[$order]."'";
			if($tab[$order] == 'For application') $s = 'selected';
			$options .= "<option ".$s." ".$t." value=".$tab['id']." '>".$m." ".$tab[$order]."</option>";
			$t = ''; $s = '';
		}
	}
	else {
		$error = true;
		$errorText .= "Error in get_select_option(".$order."-".$category.") : ".mysql_error()."<br><br>";
	}
	
	$field = "";
	$color = "";
	switch($category){//A different color in each field
		case '0': $field = "theme"; $color = "_color1"; break;
		case '1': $field = "location"; $color = "_color2"; break;
		case '2': $field = "person"; $color = "_color3"; break;
		case '3': $field = "genre"; $color = "_color5"; break;
		case '4': $field = "misc"; $color = "_color6"; break;
		case '5': $field = "system"; $color = "_color7"; break;
		default: $field = "direct_link"; $color = "_color4"; break;
	}
	
	//the input and the button to add keywords 
	$input_add = "	<input type='text' style='margin-top:3px;width:190px;color:#efefef;' id='".$field."_add' />
					<div style='position:relative;bottom:31px;left:192px;border-radius:5px;display:bloc;width:30px;height:29px;background: url(images/input.png) 0 0 repeat;border:1px solid #474b57;'>
						<img src='images/plus1.png' class='normaltip' title='Add a ".$field."' onclick=\"add_select_option('".$field."');\" />
					</div>";
	if($order == 'title') $input_add = '';//nothing for direct links
	
	$before = "<div class='grid_3'>
					<select multiple size='15' style='width:225px;color:#efefef;background: url(images/input".$color.".png) 0 0 repeat;' id='".$field."_select' >";
	
	$after = 
					"</select>
					".$input_add."
				</div>" ;
				
	return $before.$init.$options.$after ;
}

/*
ex: 
get_select_options('inb_keywords','0','keyword','') for themes
get_select_options('inb_medias_1','','title','26') for direct links except 26
*/

//a function to add new keywords (one type) to the database
function add_new_keywords_one_category($category){
	global $error, $errorText ;
	$feild = '';
	switch($category){
		case KEYWORD_LOCATION_CAT: $field = 'locations_added';break;
		case KEYWORD_PERSON_CAT: $field = 'persons_added';break;
		case KEYWORD_MISC_CAT: $field = 'misc_added';break;
		case KEYWORD_GENRE_CAT: $field = 'genres_added';break;
		case KEYWORD_SYSTEM_CAT: $field = 'systems_added';break;
		default: $field = 'themes_added';break;
	}
	
	//get information which will be regisreted in inb_themes
		//idt: automatic
		//for any keyword type, we get the information from $_POST[$field]
		if($_POST[$field] != ''){
			$tab = explode(",",$_POST[$field]);//they separated by a coma, we turn the string into an array
			foreach($tab as $one){//we add keywords one by one
				if ($one!='') {
					$query = sprintf("INSERT INTO inb_keywords(id,category,keyword) VALUES('','%d','%s')",$category,$one);
					$request = mysql_query($query);
					if(!($request)){
						$errorText .= "An error has occurred during the registering (level 2.".$category.") into the database: <br />".mysql_error()."<br /> ";
						$error = true;
					}
				}
			}
		}
}

//a function to add new keywords (all of them) to the database
function add_new_keywords(){
	$cat_array = array(KEYWORD_THEME_CAT, KEYWORD_LOCATION_CAT, KEYWORD_PERSON_CAT, KEYWORD_MISC_CAT, KEYWORD_GENRE_CAT, KEYWORD_SYSTEM_CAT);
	foreach($cat_array as $cat) //for each type, we call the function add_new_keywords_one_category
		add_new_keywords_one_category($cat);
}

//a function to update all the metadata of one media
function update_media($field){
	global $errorText, $successText, $error, $success ;
	//get information which will be regisreted in inb_medias_1
		//themes: input name themes
		//locations: input name locations
		//persons: input name persons
		//misc: input name misc
		//genre: input name genre
		//system: input name system
		//direct links: input name direct_links
		//last_modification_date: current date given by the sql function curdate()
	
	if($field != 'text')	
		$query = sprintf("UPDATE inb_medias_1 set themes='%s', locations='%s', persons='%s', misc='%s', genre='%s', system='%s', direct_links='%s', last_modification_date=CURDATE() WHERE id='%d'",
			htmlspecialchars($_POST['themes']),
			htmlspecialchars($_POST['locations']),
			htmlspecialchars($_POST['persons']),
			htmlspecialchars($_POST['misc']),
			htmlspecialchars($_POST['genre']),
			htmlspecialchars($_POST['system']),
			htmlspecialchars($_POST['direct_links']),
			$_POST['id']
			);
	else // we add the key_areas: input name key_areas
		$query = sprintf("UPDATE inb_medias_1 set key_areas='%s', themes='%s', locations='%s', persons='%s', misc='%s', genre='%s', system='%s', direct_links='%s', last_modification_date=CURDATE() WHERE id='%d'",
			htmlspecialchars($_POST['key_areas']),
			htmlspecialchars($_POST['themes']),
			htmlspecialchars($_POST['locations']),
			htmlspecialchars($_POST['persons']),
			htmlspecialchars($_POST['misc']),
			htmlspecialchars($_POST['genre']),
			htmlspecialchars($_POST['system']),
			mysql_real_escape_string(htmlspecialchars($_POST['direct_links'])),
			$_POST['id']
			);
			
	$request = mysql_query($query);
	
	if($request)
	{
		add_new_keywords();
		if(!($error)){
			$success = true;
			$successText .= "The ".$field." has been updated in the database successfully.";
			echo '<script language="Javascript" charset="utf8" >
			<!--
			document.location.replace("index.php#'.$field.'");
			// -->
			</script>';
		}
	}
	else
	{
		$errorText .= "An error has occurred during the updating of the ".$field." (level 1) into the database: <br />".mysql_error()."<br /> ";
		$error = true;
	}
	$xmlgenerated = generatexml();
	if($xmlgenerated < 0){
		$errorText .= "An error has occurred while updating the application: level ".$xmlgenerated.". <br />";
		$error = true;
	}
}


//for all upload page, this function display the select fields of keywords settings	
function display_thematic_metadata($n){
	?>
	<div id='thematic_metadata' <?php if(!isset($_GET['id']) && $n == 1) echo "style='display:none;'"; ?> >
		<div class="wrapper" style="text-align:center;" >
		
			<?php if($n == 1){ //means this is a text, we can add the key area ?>
			<div class="grid_12" style="text-align:center;min-height:40px;" id="clic_area_display" >
					Key area : &nbsp;&nbsp;<span style='font-size:1.2em;font-weight:bold;color:#ffffff'> Whole text <br></span>
			</div>
			<div class="grid_12" style="text-align:center;" >
				<div class="button" style="display:none;" id="clic_area_del_button" >Delete this clic area</div>
			</div>
			<?php } ?>
			
			<div class="grid_3">
				<h4>Themes</h4>
			</div>
			<div class="grid_3">
				<h4>Locations</h4>
			</div>
			<div class="grid_3">
				<h4>Persons</h4>
			</div>
			<div class="grid_3">
				<h4>Direct links</h4>
			</div>
			
		</div>
		
		
		<div class="wrapper" >
			<!-- we use the function we set earlier to display options -->
			<?php echo get_select_options('inb_keywords','0','keyword',''); ?>
			<?php echo get_select_options('inb_keywords','1','keyword',''); ?>
			<?php echo get_select_options('inb_keywords','2','keyword',''); ?>
			<?php $id = ""; if(isset($_GET['id'])) $id="'".$_GET['id']."'"; echo get_select_options('inb_medias_1','','title',$id); ?>
		</div>
		<div class="wrapper" style="text-align:center;" >
			<div class="grid_3">
				<h4>Genre</h4>
			</div>
			<div class="grid_3">
				<h4>Misc</h4>
			</div>
			<div class="grid_3">
				<h4>System</h4>
			</div>
			<div class="grid_3">
				&nbsp;
			</div>
		</div>
		<div class="wrapper" >
			<!-- we use the function we set earlier to display options -->
			<?php echo get_select_options('inb_keywords','3','keyword',''); ?>
			<?php echo get_select_options('inb_keywords','4','keyword',''); ?>
			<?php echo get_select_options('inb_keywords','5','keyword',''); ?>
			<div class='grid_3' >
				<div class="submit_button" style="position:relative;left:150px;cursor:pointer;border-radius:11px;display:bloc;width:50px;height:50px;background: url(images/submit50.png);">	
				</div><!-- the button to move to the next step, but here, for only submit -->
			</div>
		</div>
		
	</div>
	<?php 
}

//for all upload page, this function display 3 principal fields: title, author, date	
function display_head_metadata($field) { 
	global $tab;
	?>
	<div class="wrapper" id="<?php echo $field; ?>_head_metadata_fixed" style='height:40px;' >
		<?php if(isset($_GET['id'])){//means we are modifying a media so the 3 principal metadata are just displayed and can't be edited
			$period = '<b>Period ';
			$t = explode('~',$tab['time']);
			$begin = $t[0];
			$end = $t[1];
			if($begin != '' && $end == '') $period .= 'after </b> '.$begin;
			else if($begin == '' && $end != '') $period .= 'before </b> '.$end;
			else $period .= 'from </b> '.$begin.' <b>to</b> '.$end;
			echo "<div class='grid_4'><b>Title : </b>".$tab['title']."</div><div class='grid_4'><b>Authors : </b>".$tab['authors']."</div><div class='grid_4'>".$period."</div>"; 
			} ?> 
	</div><!-- here will be displayed the title, the authors and dates; after hiding inputs -->
	<div class="wrapper" id="<?php echo $field; ?>_head_metadata" <?php if(isset($_GET['id'])) echo "style='display:none;'"; ?> style='height:40px;' >
	
		<div class="grid_4">
			<label class="name">
				<input type="text" id="title" name="title" placeholder="Title" />
			</label>
		</div>
		<div class="grid_4">
			<label class="name">
				<input type="text" id="authors" name="authors" placeholder="Authors"/>
			</label>
		</div>
		<div class="grid_4">
			<div style="display:block;min-height:46px;" >
				Period from <input style="width:90px;" type="date" id="date_begin" onchange="$('#date_end').val($(this).val());" name="date_begin" placeholder="beginning" /> to 
				<input style="width:90px;" type="date" id="date_end" name="date_end" placeholder="end" />	
			</div>
		</div>
	</div>
	<?php
}


//for all upload page, this function display the specific fields of upload (file selector | text editor | sequences | associations)
function display_media_add_fields($field) { 
	global $tab, $error, $errorText, $except1, $except2;
	
	/* associated text : for an audio or a video*/
	$init_at="<option value=0 >  ---- associated text ----  </option>";
	$query_at = sprintf("select * from inb_medias_1 WHERE mediatype=".MEDIATYPE_TEXT." AND link_type=".LINK_TYPE_NO_LINK." ORDER by title");
	$request_at = mysql_query($query_at);
	

	$options_at = "<div class='grid_4 aligncenter' style='background: url(images/input_color4.png);border-radius:5px;height:70px;margin-bottom:15px;' >
						<span style='display:inline-block; line-height:1.2em; font-size:15px; color:#fff; text-transform:uppercase; padding:5px 20px 8px 15px; letter-spacing:1px; border-radius:2px;' > ASSOCIATED TEXT </span><br>
						<select name='associated_text' >".$init_at;
	$s = '';
	$associated_text = 0;
	if(isset($_GET['id'])){
		$query_in = sprintf("select associated_text from inb_medias_1 WHERE id='%d'", $_GET['id']);
		$request_in = mysql_query($query_in);
		$associated_text = mysql_result($request_in, 0);
	}
	if($request_at){
		while($tab_at = mysql_fetch_assoc($request_at)){
			if($tab_at['id'] == $associated_text) $s = 'selected';
			$options_at .= "<option ".$s." value=".$tab_at['id']." >".$tab_at['title']."</option>";
			$s = '';
		}
	}
	else {
		$error = true;
		$errorText .= "An error occured while gathering texts: ".mysql_error()."<br><br>";
	}
	$options_at .= "		</select>
						</div>";
	/* associated text end */
	
	/* associated audio : For a text */
	$init_aa="<option value=0 >  ---- associated audio ----  </option>";
	$query_aa = sprintf("select * from inb_medias_1 WHERE mediatype=".MEDIATYPE_AUDIO." AND link_type=".LINK_TYPE_RELATIVE." ORDER by title");
	$request_aa = mysql_query($query_aa);
	
	if($field=='srt') $weird_debug = "onchange=\"$('#audio').val($(this).val());\""; else $weird_debug = '';//a weird way tofix a problem due to srt files processing 
	$options_aa = "<div class='grid_4 aligncenter' style='background: url(images/input_color4.png);border-radius:5px;height:70px;margin-bottom:15px;' >
						<span style='display:inline-block; line-height:1.2em; font-size:15px; color:#fff; text-transform:uppercase; padding:5px 20px 8px 15px; letter-spacing:1px; border-radius:2px;' > Associated audio </span><br>
						
						<select name='associated_audio' ".$weird_debug." />".$init_aa;
	$s = '';
	$associated_audio = 0;
	if(isset($_GET['id'])){
		$query_in = sprintf("select associated_audio from inb_medias_1 WHERE id='%d'", $_GET['id']);
		$request_in = mysql_query($query_in);
		$associated_audio = mysql_result($request_in, 0);
	}
	if($request_aa){
		while($tab_aa = mysql_fetch_assoc($request_aa)){
			if($tab_aa['id'] == $associated_audio) $s = 'selected';
			$options_aa .= "<option ".$s." value=".$tab_aa['id']." >".$tab_aa['title']."</option>";
			$s = '';
		}
	}
	else {
		$error = true;
		$errorText .= "An error occured while gathering audio tracks: ".mysql_error()."<br><br>";
	}
	$options_aa .= "		</select>
						</div>";
	/* associated audio end */
	
	/* associated video : For a text*/
	$init_av="<option value=0 >  ---- associated video ----  </option>";
	$query_av = sprintf("select * from inb_medias_1 WHERE mediatype=".MEDIATYPE_VIDEO." AND link_type=".LINK_TYPE_RELATIVE." ORDER by title");
	$request_av = mysql_query($query_av);
	
	if($field=='srt') $weird_debug = "onchange=\"$('#video').val($(this).val());\""; else $weird_debug = '';//a weird way tofix a problem due to srt files processing 
	$options_av = "<div class='grid_4 aligncenter' style='background: url(images/input_color4.png);border-radius:5px;height:70px;margin-bottom:15px;' >
						<span style='display:inline-block; line-height:1.2em; font-size:15px; color:#fff; text-transform:uppercase; padding:5px 20px 8px 15px; letter-spacing:1px; border-radius:2px;' > Associated video </span><br>
						
						<select name='associated_video' ".$weird_debug." />".$init_av;
	$s = '';
	$associated_video = 0;
	if(isset($_GET['id'])){
		$query_in = sprintf("select associated_video from inb_medias_1 WHERE id='%d'", $_GET['id']);
		$request_in = mysql_query($query_in);
		$associated_video = mysql_result($request_in, 0);
	}
	if($request_av){
		while($tab_av = mysql_fetch_assoc($request_av)){
			if($tab_av['id'] == $associated_video) $s = 'selected';
			$options_av .= "<option ".$s." value=".$tab_av['id']." >".$tab_av['title']."</option>";
			$s = '';
		}
	}
	else {
		$error = true;
		$errorText .= "An error occured while gathering videos: ".mysql_error()."<br><br>";
	}
	$options_av .= "		</select>
						</div>";
	/* associated video end */
		
	/* Arch : For a text, a select field named extract_of */
	$init_arch="<option value=0 >  ---- text extracted from ----  </option>";
	$query_arch = sprintf("select * from inb_medias_1 WHERE mediatype=".MEDIATYPE_TEXT." AND link_type=".LINK_TYPE_RELATIVE." ORDER by title");
	$request_arch = mysql_query($query_arch);
	

	$options_arch = "<div class='grid_4 aligncenter' style='background: url(images/input_color4.png);border-radius:5px;height:70px;margin-bottom:15px;' >
						<span style='display:inline-block; line-height:1.2em; font-size:15px; color:#fff; text-transform:uppercase; padding:5px 20px 8px 15px; letter-spacing:1px; border-radius:2px;' > Extract of </span><br>
						<select name='extract_of' >".$init_arch;
	$s = '';
	$extract_of = 0;
	if(isset($_GET['id'])){
		$query_in = sprintf("select extract_of from inb_medias_1 WHERE id='%d'", $_GET['id']);
		$request_in = mysql_query($query_in);
		$extract_of = mysql_result($request_in, 0);
	}
	if($request_arch){
		while($tab_arch = mysql_fetch_assoc($request_arch)){
			if($tab_arch['id'] == $extract_of) $s = 'selected';
			$options_arch .= "<option ".$s." value=".$tab_arch['id']." >".$tab_arch['title']."</option>";
			$s = '';
		}
	}
	else {
		$error = true;
		$errorText .= "An error occured while gathering archives: ".mysql_error()."<br><br>";
	}
	$options_arch .= "		</select>
						</div>";
	/* Arch end */
	
	
	if($field == 'text'){
		$before = "<div class='wrapper' id='head_metadata_selects' >";
		$after = "</div>" ;
					
		echo $before.$options_arch.$options_aa.$options_av.$after ;
		if(isset($_GET['id'])) $except1 = $_GET['id'];//the used media is an exception to this select field
		
		
		
		?>
		<!-- Here a script to update the 'next text' field including the exception of the text set aa the previous -->
		<script type="text/javascript" charset="utf8" >
			$("#head_metadata_selects").append("<div class='grid_4 aligncenter' style='background: url(images/input_color4.png);border-radius:5px;height:70px;margin-bottom:15px;' ><span style='display:inline-block; line-height:1.2em; font-size:18px; color:#fff; text-transform:uppercase; padding:5px 20px 8px 15px; letter-spacing:1px; border-radius:2px;' > Text sequence >>> </span></div>");
			$("#head_metadata_selects").append(ajax_open("inc/sequence_options.php?except1=<?php echo $except1; ?>&except2=<?php echo $except2; ?>&selected=<?php echo $tab['previous']; ?>&name=previous"));
			$("#head_metadata_selects").append("<div id='next_div' >"+ajax_open("inc/sequence_options.php?except1=<?php echo $except1; ?>&except2=<?php echo $except2; ?>&selected=<?php echo $tab['next']; ?>&name=next")+"</div>");
			
							
			$("#previous").change( function(){
				$("#next_div").html(ajax_open("inc/sequence_options.php?except1=<?php echo $except1; ?>&except2="+$(this).val()+"&selected=<?php echo $tab['next']; ?>&name=next"));
			});
		</script>
		
		
		<div class="wrapper" style='min-height:60px;' >
			<div class="grid_12" style="margin-top:5px;" >
				<p><textarea style="width:880px;<?php if(isset($_GET['id'])) echo 'display:none;'; ?>" name="text_content" id='text_content' placeholder="Type the text ..." ></textarea></p>
			</div>
			<div class="submit_button" style="position:relative;top:5px;left:910px;cursor:pointer;border-radius:11px;display:bloc;width:50px;height:50px;<?php if(isset($_GET['id'])) echo "background: url(images/submit50.png);"; else echo "background: url(images/next50.png);"; ?>">	
			</div><!-- the button to move to the next step -->
			<div id="added_text_bloc" class="grid_12" <?php if(!isset($_GET['id'])) echo "style='display:none;'"; ?> >
				<p id="added_text" onmouseup="state_transition(getSelectedText());" ><?php if(isset($_GET['id'])) echo html_entity_decode($tab['text']); ?> </p><!-- he will be displayed the modified text  -->
			</div>
		</div>
		<?php
	}
	else {?>
		<div class="wrapper" >
			<br>
			<?php
			if($field == 'audio' || $field == 'video'){
				echo $options_at ;
			}
			else{
				echo "<div class='grid_4' id='srt_type_div' >&nbsp;</div>
					  <div id='needed_for_srt' style='display:none;'>".$options_aa.$options_av."</div>";
			}
			?>
			<div class='grid_4' style='text_align:center;<?php if(isset($_GET['id'])) echo 'display:none;'; ?>' ><br>
				<input type="file" name="thefile" id='thefile' />
				<div class="submit_button" style="position:relative;bottom:15px;left:510px;cursor:pointer;border-radius:11px;display:bloc;width:50px;height:50px;background: url(images/submit50.png);" ></div>	
				<script charset='utf-8' type='text/javascript' >
					$('#thefile').change(function(){
						var tab_name = explode('.',$("#thefile")[0].files[0].name);
						var title = '';
						var s = '';
						for(var i = 0 ; i < (tab_name.length - 1) ; i++){
							title += s+tab_name[i];
							s = '.';
						}
						$('#title').val(title);
					});
				</script>
			</div>
		</div>
		<?php
	}
}
	
//for all upload page, this function display the title and a notificication box(if there is one)
function display_notification_box_and_title($title) {
	global $success,$successText,$error,$errorText;
	?>
	<script type="text/javascript" charset="utf8" >
		//when there is a notification, it must be hidden after few secondes. This function hide them
		function hide_notifications(){
			$('#notifications').slideUp(1500);
		}
		//this variable check if notification will be displayed
		var hide = <?php if($error or $success) echo 'true'; else echo 'false'; ?>;
		
		
	</script>
	<div class="wrapper" >
		<div class="grid_12" style="text-align:center;" >
			<h1><?php if(!isset($_GET['id'])) echo "Add ".$title; else echo "Edit ".$title; ?></h1>
		</div>
	</div>
	
	<div class="wrapper" id="notifications" >
		<div class="grid_3" >&nbsp;</div>
		<div class="grid_6" >
			<?php if($success) { ?>
				<div class='download-box' style='width:350px;border-radius:10px;' >
					<p class='icon'><img src='images/icon-download.png' ></p>
					<?php echo $successText; ?>
				</div>
			<?php } if($error) { ?>
				<div class='error-box' style='width:350px;border-radius:10px;' >
					<p class='icon'><img src='images/icon-error.png' ></p>
					<?php echo $errorText; ?>
				</div>
			<?php } ?>
		</div>		
	</div>	
	<script type="text/javascript" charset="utf8" > if(hide) setTimeout("hide_notifications()",3000); </script>
	<?php
}
	
//this function return the javascript code to initiate some variables used for the add of new keywords	
function echo_options_add_value_init($field){
	?>
	<script type="text/javascript" charset="utf8" >	

		var options_values = new Array();//An array to store new options added (ex: new themes)
		//a choice: start storing new options from the index 1, so the index 0 is empty
		options_values['theme'] = new Array('');
		options_values['location'] = new Array('');
		options_values['person'] = new Array('');
		options_values['genre'] = new Array('');
		options_values['misc'] = new Array('');
		options_values['system'] = new Array('');
		
		//For any option, his value is his Id in the DB, So when we'll add an option, his value must be higher then all previous options Id; and different.
		var max_value = <?php echo mysql_result(mysql_query("SELECT MAX(id) FROM inb_keywords"),0); ?> ;
		
		//this array store the current higher id; according to the added options
		var current_value = max_value;
	
	</script>
	<script type="text/javascript" charset="utf8" src="js/thematics.min.js" > </script>
	<script type="text/javascript" charset="utf8" src="js/<?php echo $field; ?>_add.js" > </script>
	<?php
}

//this function return the javascript code to set the submit function:
//for a text: check some fields, add kew words, create key area string
//for a file: check some fields and file extension , add kew words, display the upload progress bar
function echo_submit_function($process,$allowed_extensions){
	if($allowed_extensions != '') {
	?>
	<script type="text/javascript" charset="utf8" >
		//here we gonna set actions when someone click on green buttons: SUBMIT
		$(".submit_button").click(function(){
			var title = $("#title").val().replace(/(\r\n|\n\r|\r|\n|\||~)/g,"");;
			title = title.replace(/("|¨)/g,"`");
			title = trim(title);
			
			var authors = $("#authors").val().replace(/(\r\n|\n\r|\r|\n|\||~)/g,"");;
			authors = authors.replace(/("|¨)/g,"`");
			authors = trim(authors);
			
			$("#title").val(title.toString());
			$("#authors").val(authors.toString());
					
			if(!edit){
				//if someone click on "SUBMIT"
				//we check if a title has been entered, else we display a warning 
				if($("#title").val() == ''){
					$('#warning').css("display", 'inline-block');
					$('#warning').html("Please enter a title") ;
					$( "#dialog:ui-dialog" ).dialog( "destroy" );
					$( "#warning" ).dialog({resizable: false,width: 150,modal: true,show: 'slide',hide: 'clip',	buttons: {
							"Ok": function() {
								$( this ).dialog( "close" );
							}
						}
					});
					$( "#warning" ).dialog('open');
				}
				else if(($("#date_begin").val() != '' && $("#date_end").val() != '' ) && ((Date.parse($("#date_end").val()) - Date.parse($("#date_begin").val()))<0)) {
					$('#warning').css("display", 'inline-block');
					$('#warning').html("The end date must be after the begin date.");
					$( "#dialog:ui-dialog" ).dialog( "destroy" );
					$( "#warning" ).dialog({resizable: false,width: 210,modal: true,show: 'slide',hide: 'clip',	buttons: {
							"Ok": function() {
								$( this ).dialog( "close" );
							}
						}
					});
					$( "#warning" ).dialog('open');
				}
				else if($("#thefile")[0].files.length < 1) {
					$('#warning').css("display", 'inline-block');
					$('#warning').html("Please, choose a file.");
					$( "#dialog:ui-dialog" ).dialog( "destroy" );
					$( "#warning" ).dialog({resizable: false,width: 210,modal: true,show: 'slide',hide: 'clip',	buttons: {
							"Ok": function() {
								$( this ).dialog( "close" );
							}
						}
					});
					$( "#warning" ).dialog('open');
				}
				else{
					//we do the last updates on the thematics arrays
					save_principal_keywords();
					//then we generate inputs
					generate_inputs();
					//and submit it
				
					var allowedExtentions = [<?php echo $allowed_extensions; ?>];
					var tab_name = explode('.',$("#thefile")[0].files[0].name);
					var ext = tab_name[tab_name.length - 1].toLowerCase();//the extension
					if(array_search (ext, allowedExtentions)>=0){//check the extension
						var size = $("#thefile")[0].files[0].size ;//get file size
						if(size < 300000000){ //check the file < 300 MB
							var ratio = 0;
							var first_zero = true; //this variable help to make difference between the upload step, and the data processing step
							$("#upload_progress").html("Uploading progress: "+ratio+"% ...");	
							$("#upload_file").html($("#thefile")[0].files[0].name);					
							$(function() {
								var pGress = setInterval(function() {
									var progress = ajax_open("inc/whileuploading.php"); //the current size of the uploading file
									ratio = Math.round((progress/size)*10000)/100; //calculates the upload percentage 	
									var pVal = $('#bar').progressbar('option', 'value');
									var pCnt = ratio;
									if(ratio > 0) first_zero = false;
									if(first_zero || ratio != 0) $("#upload_progress").html("Uploading progress: "+ratio+" % ...");	//upload step
									else {$("#upload_progress").html('<?php echo $process; ?>'); pCnt = 100 ;}	//data processing step
									if (pCnt > 100) {
										clearInterval(pGress);
									} else {
										$('#bar').progressbar({value: pCnt});
									}
								},100);//progress bar updated every 100 ms
							});
							$('#upload').css("display", 'inline-block');
							$( "#dialog:ui-dialog" ).dialog( "destroy" );
							$( "#upload" ).dialog({resizable: false,width: 420,modal: true,show: 'highlight',hide: 'highlight'});
							$( "#upload" ).dialog('open');
							document.add_form.submit();	// then we can submit
						}
						else{
							$('#warning').css("display", 'inline-block');
							$('#warning').html("This file ( "+$("#thefile")[0].files[0].name+" ) size is too heavy. The limit is 300 MB per file.");
							$( "#dialog:ui-dialog" ).dialog( "destroy" );
							$( "#warning" ).dialog({resizable: false,width: 500,modal: true,show: 'slide',hide: 'clip',	buttons: {
									"Ok": function() {
										$( this ).dialog( "close" );
									}
								}
							});
							$( "#warning" ).dialog('open');
						}
					}
					else
					{
						$('#warning').css("display", 'inline-block');
						$('#warning').html("This file ( "+$("#thefile")[0].files[0].name+" ) type is not allowed here. Please choose one of these file types: "+implode(", ",allowedExtentions)+".");
						$( "#dialog:ui-dialog" ).dialog( "destroy" );
						$( "#warning" ).dialog({resizable: false,width: 500,modal: true,show: 'slide',hide: 'clip',	buttons: {
								"Ok": function() {
									$( this ).dialog( "close" );
								}
							}
						});
						$( "#warning" ).dialog('open');
					}
				}
			}
			else{
				//we do the last updates on the thematics arrays
				save_principal_keywords();
				//then we generate inputs
				generate_inputs();
				//and submit it
				document.add_form.submit();	
			}
		});
	</script>
	<?php
	}
	else {
	?>
		<script type="text/javascript" charset="utf8" >
			//here we gonna set actions when someone click on green buttons: NEXT and SUBMIT
			$(".submit_button").click(function(){
				//if edit_text means we are about clicking on "NEXT"
				if(edit_text){
					//in the text, a carriage return or a line feed can cause some bugs, so we remove them
					//we reserve us the next characters: | and ~. So we remove them from all inputs 
					text = document.getElementById("text_content").value;
					text = text.replace(/("|¨)/g,"`");
					text = text.replace(/(&)/g,"& ");
					text = text.replace(/(<)/g,"< ");
					text = text.replace(/(>)/g," >");
					text = text.replace(/(\r\n|\n\r|\r|\n|\||~)/g,"<br><br>");
					text = trim(text);
					text = text;
					
					
					var title = $("#title").val().replace(/(\r\n|\n\r|\r|\n|\||~)/g,"");;
					title = title.replace(/(")/g,"`");
					title = trim(title);
					$("#title").val(title);
					
					
					var authors = $("#authors").val().replace(/(\r\n|\n\r|\r|\n|\||~)/g,"");;
					authors = authors.replace(/(")/g,"`");
					authors = trim(authors);
					$("#authors").val(authors);
					
					//we check if a title has been entered, else we display a warning 
					if(title == ''){
						$('#warning').css("display", 'inline-block');
						$('#warning').html("Please enter a title") ;
						$( "#dialog:ui-dialog" ).dialog( "destroy" );
						$( "#warning" ).dialog({resizable: false,width: 150,modal: true,show: 'slide',hide: 'clip',	buttons: {
								"Ok": function() {
									$( this ).dialog( "close" );
								}
							}
						});
						$( "#warning" ).dialog('open');
					}
					//then we check if the text has been entered
					else if(text == ''){
						$('#warning').css("display", 'inline-block');
						$('#warning').html("You forgot the text !");
						$( "#dialog:ui-dialog" ).dialog( "destroy" );
						$( "#warning" ).dialog({resizable: false,width: 150,modal: true,show: 'slide',hide: 'clip',	buttons: {
								"Ok": function() {
									$( this ).dialog( "close" );
								}
							}
						});
						$( "#warning" ).dialog('open');
					}
					else if(($("#date_begin").val() != '' && $("#date_end").val() != '' ) && ((Date.parse($("#date_end").val()) - Date.parse($("#date_begin").val()))<0)) {
						$('#warning').css("display", 'inline-block');
						$('#warning').html("The end date must be after the begin date.");
						$( "#dialog:ui-dialog" ).dialog( "destroy" );
						$( "#warning" ).dialog({resizable: false,width: 210,modal: true,show: 'slide',hide: 'clip',	buttons: {
								"Ok": function() {
									$( this ).dialog( "close" );
								}
							}
						});
						$( "#warning" ).dialog('open');
					}
					//if all is ok, we can get to the next step: edit the thematics
					else{
						var period = '<b>Period ';
						var begin = $("#date_begin").val();
						var end = $("#date_end").val();
						if(begin != '' && end == '') period += 'after </b> '+begin;
						else if(begin == '' && end != '') period += 'before </b> '+end;
						else period += 'from </b> '+begin+' <b>to</b> '+end;				
						
						//we uptade the initial text
						$("#text_content").val(text);
						
						//the we set visible text whichwill be displayedint this step, which can be colored
						text_visible = text;
						
						//a little animation to hide inputs and display information
						$("#text_content").slideUp('fast', function(){
							$("#text_head_metadata").slideUp('fast', function(){
								$("#added_text").html(text);
								$("#title").val(title.toString());
								$("#authors").val(authors.toString());
								$("#text_head_metadata_fixed").html("<div class='grid_4' style='height:40px;' ><b>Title : </b>"+title+"</div><div class='grid_4'><b>Authors : </b>"+authors+"</div><div class='grid_4'>"+period+"</div>");
								$(".submit_button").css("background","url(images/submit50.png)");
								$("#text_head_metadata_fixed").slideDown('slow', function(){
									$("#added_text_bloc").slideDown('slow', function(){
										$("#thematic_metadata").slideDown('slow');
									});
								});	
							});
						});
						edit_text = false;
					}
				}
				//if someone click on "SUBMIT"
				else{
					//we do the last updates on the thematics arrays
					perform_action(state,CLIC_AREA.ON_TEXT,-2,'');
					//then we generate inputs
					generate_inputs();
					//and submit it
					document.add_form.submit();
				}
			});
		</script>
	<?php
	}
}


//if a media is being edited, this function will load set the keyword selection according to the metadata on the media
function load_principal_keywords(){
	global $tab;
	?>
	<script type="text/javascript" charset="utf8" >
		var tabx1 = [] ; var tabx2 = [] ; var tabx3 = [] ; var tabx4 = [] ; var tabx5 = [] ; var tabx6 = [] ; var tabx7 = [] ;
		var tabx_1 = [] ; var tabx_2 = [] ; var tabx_3 = [] ; var tabx_4 = [] ; var tabx_5 = [] ; var tabx_6 = [] ; var tabx_7 = [] ;
		
		//the media witch we are currently editing mustn't be in this list
		tab_direct_link = explode(',',ajax_open("inc/options.php?cat=-1&except=<?php echo $_GET['id']; ?>"));
		tab_direct_link = [0].concat(tab_direct_link);
		tab_keywords = [tab_theme,tab_location,tab_person,tab_genre,tab_misc,tab_system,tab_direct_link];
	</script>
	
	<?php
	$keywords = array($tab['themes'],$tab['locations'],$tab['persons'],$tab['genre'],$tab['misc'],$tab['system'],$tab['direct_links']);
	for($i = 1 ; $i <= 7 ; $i++){
		if ($keywords[$i-1]!=''){
			$tab2 = explode(',',$keywords[$i-1]);
			foreach($tab2 as $one){ 
				if($one != '') { ?> <script type="text/javascript" charset="utf8" > tabx<?php echo $i; ?>.push('<?php echo $one; ?>'); </script>
				<?php }
			}
			?>
			<script type="text/javascript" charset="utf8" >
			for(var i = 0 ; i < tab_keywords[<?php echo ($i-1); ?>].length ; i++){
				if(array_search(tab_keywords[<?php echo ($i-1); ?>][i],tabx<?php echo $i; ?>)>=0)	tabx_<?php echo $i; ?>.push('1');
				else tabx_<?php echo $i; ?>.push('0');	
			}
			</script>
			<?php 
		}	
	}
	?>
	
	<script type="text/javascript" charset="utf8" >
		tab_of_keywords.push(tabx_1, tabx_2, tabx_3, tabx_4, tabx_5, tabx_6, tabx_7);
		$('#add_form').append("<input type='hidden' name='id' value='<?php echo $_GET['id'] ; ?>' />");
	</script>	
	
	<?php
}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	