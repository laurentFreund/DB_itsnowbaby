<?php

################################################################################################
/**********************************************************************************************\
|**		Document:					settings.php                                             **|
|**		Creation:					April 04, 2012                                           **|
|**		Last modification:			June 27, 2012                                            **|
|**		Authors:					Abdoul aziz SENE                                         **|
|**		Project:					It's Now Baby, Industrial project 7, EMSE & LFKs		 **|
|**		Company:					EMSE, Cycle ISMIN, Promo 2013                            **|
|**		Description:				Setting backgrounds and keywords                         **|
\**********************************************************************************************/
################################################################################################

	$current_menu = 3;//to display 'SETTINGS' menu as selected
	$title = utf8_encode("Settings");//title for the window
	include("header.php");//load the header

	//vérification de la connexion admin
	if(!isset($_SESSION['admin']))
	{
		echo '<script language="Javascript">
		<!--
		document.location.replace("index.php");
		// -->
		</script>';
		echo 'Vous devez être identifié pour accéder à cette page!';
		exit();
	}
	
//some variables for DB success in process	
$error = false;
$errorText = "";

//connect to DB	
include("inc/connexionbdd.php");
Connexion_BDD();

//load functions file for images
include("inc/image.functions.php");

//if a background has been chhosed
if(isset($_POST['background']))
{
	if($_FILES['background_'.$_POST['background']]['error']  !== UPLOAD_ERR_OK){//if there was error during uploading
		$errorText .= "There is no file to upload.<br /> ";
		$error = true;
	}
	else if (is_uploaded_file($_FILES['background_'.$_POST['background']]['tmp_name'])) //else if the upload was successfull
	{
		$nameOriginal = $_FILES['background_'.$_POST['background']]['name'];//te file's name
		$elementsWay = pathinfo($nameOriginal);//the file's path
		$extensionFile = strtolower($elementsWay['extension']);//the file's extention
		$allowedExtensions = array('gd','gd2','gif','jpeg','jpg','wbmp','xbm','xpm','png'); // the list of allowed extensions for the cenverter or the application
		if(in_array($extensionFile,$allowedExtensions))// if the file's extention is OK, we can add it to our database
		{
			$directoryDestination = dirname(__FILE__)."/";//the current path
			$id = "bg";
			$title = $_POST['background']."-".date('U');//useless date, just to make a difference between this image and the one in the browser's cache memory
			$nameDestination = $id."_".$title.".".$extensionFile;//new file's name 
			$relative_link = "medias/images/".$nameDestination ;// new file's name in his path
			$link = $directoryDestination.$relative_link ;// root path of the file
			if(!move_uploaded_file($_FILES['background_'.$_POST['background']]['tmp_name'] , $link)){//we move the file to his new path
				echo "An error has occurred during the file move<br />";
			}
			else
			{	
				//we resize the image into low, medium and high resolution, then we convert them into png
				$low = image_resize_and_save(LOW_RESOLUTION_IMG_WIDTH, LOW_RESOLUTION_IMG_HEIGHT, "medias/images/low/", 0);
				if(!isset($low)){
					$errorText .= "The low resolution image has not been created.<br /> ";
					$error = true;
				}
				$medium = image_resize_and_save(MEDIUM_RESOLUTION_IMG_WIDTH, MEDIUM_RESOLUTION_IMG_HEIGHT, "medias/images/medium/", 0);
				if(!isset($medium)){
					$errorText .= "The medium resolution image has not been created.<br /> ";
					$error = true;
				}
				$high = image_resize_and_save(HIGH_RESOLUTION_IMG_WIDTH, HIGH_RESOLUTION_IMG_HEIGHT, "medias/images/high/", 0);
				if(!isset($high)){
					$errorText .= "The high resolution image has not been created.<br /> ";
					$error = true;
				}
				$same = image_resize_and_save(0, 0, "medias/images/original/", 1);
				if(!isset($same)){
					$errorText .= "The original image has not been converted to png.<br /> ";
					$error = true;
				}
				
				//delete the old background
				$queryb = sprintf("SELECT background FROM inb_keywords WHERE id='%d'", $_POST['background']);
				$requestb = mysql_query($queryb);
				$old_bg = mysql_result($requestb,0);
				if($old_bg != '')
					$sep = ','; 
				else
					$sep = ''; 
				
				//get information which will be regisreted in inb_keywords 
					//id: automatic
					//background: file name

				$query1 = sprintf("UPDATE inb_keywords SET background='%s' WHERE id='%d'", $old_bg.$sep.$nameDestination,$_POST['background']);
				$request1 = mysql_query($query1);
				
				if($request1)
				{
					if(!($error)){
						$success = true;
						$successText .= "The background has been registered into the database successfully.";
					}
				}
				else
				{
					$errorText .= "An error has occurred during the registering (level 1) into the database: <br />".mysql_error()."<br /> ";
					$error = true;
				}
			}
		}
		else
		{
			$errorText .= "'".$_FILES['background_'.$_POST['background']]['name']."' is an invalide file type. Please choose one of these file types: ".implode(', ',$allowedExtensions)." etc.<br /> ";
			$error = true;
		}
	}
}


function display_keywords($cat){
	global $error,$errorText;
	$field = ''; 
	switch($cat){
		case KEYWORD_THEME_CAT: $field = 'theme'; break;
		case KEYWORD_LOCATION_CAT: $field = 'location'; break;
		case KEYWORD_PERSON_CAT: $field = 'person'; break;
		case KEYWORD_MISC_CAT: $field = 'misc'; break;
		case KEYWORD_GENRE_CAT: $field = 'genre'; break;
		case KEYWORD_SYSTEM_CAT: $field = 'system'; break;
		default: $field = ''; break;
	}
	
	$query = sprintf("SELECT * FROM inb_keywords WHERE category=%d ORDER BY keyword",$cat);
	$request = mysql_query($query);
	if(!($request))
	{
		$errorText .= "An error has occurred during the information gathering (level 0) from the database: <br />".mysql_error()."<br /> ";
		$error = true;
	}
	$message = '';
	if(mysql_num_rows($request) <= 0) 
		$message = "
		<div class='wrapper' >
			<div class='grid_3' >&nbsp;</div>
			<div class='grid_6' >
				<div class='info-box' style='width:350px;border-radius:10px;' >
					<p class='icon'><img src='images/icon-info.png' ></p>
					No ".$field." registered yet.
				</div>
			</div>
		</div>";
		
	
	
	?>
	<div id='<?php echo $field; ?>' >
		<div class="wrapper" >
			<?php
			echo $message;
			while($tab = mysql_fetch_assoc($request)){
				?>
				<div id="<?php echo $field."_".$tab['id']; ?>" >
					<div class="grid_5" style="background: url('images/input.png'); margin:1px; font-size:1.3em;" >
						<input type='text' style="width:320px;" value='<?php echo $tab['keyword']; ?>' onkeyup="update_keyword(<?php echo $tab['id'].",".$tab['category'].",this.value"; ?>);" />
						<img src='images/del15.png' style='position:relative; top:9px;' class="normaltip floatright" title='Delete' onclick="delete_keyword(<?php echo $tab['id'].",".$tab['category'].",'".$field."_".$tab['id']."'"; ?>);" />
					</div>
				</div>
				<?php 
			} 	
			?>	
		</div>
	</div>
	
	<?php
	
}
?>

	<script type="text/javascript" charset="utf8" >
		//when there is a notification, it must be hidden after few secondes. This function hide them
		function hide_notifications(){
			$('#notifications').slideUp(1500);
		}
		//this variable check if notification will be displayed
		var hide = <?php if($error or $success) echo 'true'; else echo 'false'; ?>;
		
		
	</script>
	
	<div class="wrapper" id="notifications" >
		<div class="grid_3" >&nbsp;</div>
		<div class="grid_6" >
			<?php if($error) { ?>
				<div class='error-box' style='width:350px;border-radius:10px;' >
					<p class='icon'><img src='images/icon-error.png' ></p>
					<?php echo $errorText; ?>
				</div>
			<?php } ?>
		</div>		
	</div>	
	<script type="text/javascript" charset="utf8" > if(hide) setTimeout("hide_notifications()",3000); </script>
	
	<br>
	
<script type="text/javascript" charset="utf8" >

	var warning = false;
	function close_warning(){
		if(warning){
			$("#warning").dialog( 'close' );
			warning = false;
		}
	}
	
	//delete the keyword using delete_keywords.php to realize operations on the db 
	function delete_keyword(id,cat,div) {
		var first_click = true;
		$('#warning').css("display", 'inline-block');
		$('#warning').html("Do you really want to delete this keyword ? (id "+id+")");
		$( "#dialog:ui-dialog" ).dialog( "destroy" );
		$( "#warning" ).dialog({resizable: false,width: 210,modal: true,show: 'slide',hide: 'clip',	buttons: {
				"No": function() {
					$( this ).dialog( 'close' );
				},
				"Yes": function() {//if the user say yes, we can delete 
					if(first_click){
						var action = ajax_open("inc/update_keywords.php?del=1&id="+id+"&cat="+cat);//using delete_keywords.php to realize operations on the db
						if (action>0) {// if the answer is positive, it'is ok
							$('#'+div).slideUp('fast');
							$( "#warning" ).dialog({resizable: false,width: 210,modal: true,show: 'slide',hide: 'clip',	buttons: {
								"Ok": function() {
									$( this ).dialog( 'close' );
									warning = false;
								}
							}});
							$('#warning').html("The keyword has been successfully deleted.");
							warning =true;
							setTimeout("close_warning()",1000);
						}
						else {// the answer is negative, a problem occured
							$( "#warning" ).dialog({resizable: false,width: 210,modal: true,show: 'slide',hide: 'clip',	buttons: {
								"Ok": function() {
									$( this ).dialog( 'close' );
									warning = false;
								}
							}});
							$('#warning').html("A problem occured while deleting : level "+action);
						}
						first_click = false;
					}
				}
			}
		});
		$( "#warning" ).dialog('open');
	}
	
	//update the keyword using delete_keywords.php to realize operations on the db 
	function update_keyword(id,cat,val) {
		var action = ajax_open("inc/update_keywords.php?id="+id+"&val="+val+"&cat="+cat);//using delete_keywords.php to realize operations on the db 
		if (action<=0) {// the answer is negative, a problem occured
			$('#warning').html("A problem occured while updating : level "+action);
			$( "#dialog:ui-dialog" ).dialog( "destroy" );
			$( "#warning" ).dialog({resizable: false,width: 210,modal: true,show: 'slide',hide: 'clip',	buttons: {
					"Ok": function() {
						$( this ).dialog( 'close' );
					}
				}
			});
			$( "#warning" ).dialog('open');
		}
	}
	
	//delete the keyword using delete_keywords.php to realize operations on the db 
	function delete_bg(id,name,name2) {
		var first_click = true;
		$('#warning').css("display", 'inline-block');
		$('#warning').html("Do you really want to delete this background ?");
		$( "#dialog:ui-dialog" ).dialog( "destroy" );
		$( "#warning" ).dialog({resizable: false,width: 210,modal: true,show: 'slide',hide: 'clip',	buttons: {
				"No": function() {
					$( this ).dialog( 'close' );
				},
				"Yes": function() {//if the user say yes, we can delete 
					if(first_click){
						var action = ajax_open("inc/delete_background.php?id="+id+"&name="+name+'.'+name2);//using delete_keywords.php to realize operations on the db
						if (action>0) {// if the answer is positive, it'is ok
							$('#'+name+'div').slideUp('fast');
							$( "#warning" ).dialog({resizable: false,width: 210,modal: true,show: 'slide',hide: 'clip',	buttons: {
								"Ok": function() {
									$( this ).dialog( 'close' );
									warning = false;
								}
							}});
							$('#warning').html("The background has been successfully deleted.");
							warning =true;
							setTimeout("close_warning()",1000);
						}
						else {// the answer is negative, a problem occured
							$( "#warning" ).dialog({resizable: false,width: 210,modal: true,show: 'slide',hide: 'clip',	buttons: {
								"Ok": function() {
									$( this ).dialog( 'close' );
									warning = false;
								}
							}});
							$('#warning').html("A problem occured while deleting : level "+action);
						}
						first_click = false;
					}
				}
			}
		});
		$( "#warning" ).dialog('open');
	}
</script>
	
	
	<div id='settings' >
		<ul>
			<li><a href='#backgrounds' > Backgrounds </a></li>
			<li><a href='#keywords' > Keywords </a></li>
		</ul>
		<div id='backgrounds' >
			<?php
			$query = sprintf("SELECT * FROM inb_keywords WHERE category='%d' ORDER BY keyword",KEYWORD_THEME_CAT);
			$request = mysql_query($query);
			if(!($request))
			{
				$errorText .= "An error has occurred during the information gathering (themes) from the database: <br />".mysql_error()."<br /> ";
				$error = true;
			}
			else{
				$message = '';
				if(mysql_num_rows($request) <= 0) 
					$message = "
					<div class='wrapper' >
						<div class='grid_3' >&nbsp;</div>
						<div class='grid_6' >
							<div class='info-box' style='width:350px;border-radius:10px;' >
								<p class='icon'><img src='images/icon-info.png' ></p>
								No theme registered yet.
							</div>
						</div>
					</div>";
				echo $message;
				
				while($tab = mysql_fetch_assoc($request)){ 
					$previews = '';
					$nb_bg = 'No background';
					if($tab['background'] != ''){
						$tab_bg = explode(',',$tab['background']);
						$nb_bg = sizeof($tab_bg)." background(s)";
						foreach($tab_bg as $one_bg){
							$name_tab = explode('.',$one_bg);
							$link_low = 'medias/images/original/'.$one_bg;
							$link_high = 'medias/images/original/'.$one_bg;
							$size = getimagesize($link_low);
							if(($size[0]/$size[1]) > (210/200)){//means that the width is the overflow factor
								// then we check if the image is too large for our div tag. In that case, we reduce the image with an alpha factor
								if($size[0]>210)
									$alpha = 210/$size[0];
								else
									$alpha = 1;
							}
							else{//means that the height may be the overflow factor
								// then we check if the image is too large for our div tag. In that case, we reduce the image with an alpha factor
								if($size[1]>200)
									$alpha = 200/$size[1];
								else
									$alpha = 1;
							}
							// as we have our alpha factor, we can figure out the new size of our image
							$image_height = $size[1] * $alpha ;
							$image_width = $size[0] * $alpha ;
							// then we display our image		
							$previews .= "	<div class='grid_2' id='".$name_tab[0]."div' onmouseover=\"$('#".$name_tab[0]."').css('display','inline');$(this).css('background','rgba(255,255,255,0.2)');\" 
																onmouseout=\"$('#".$name_tab[0]."').css('display','none');$(this).css('background','none');\" 
																style='height:200px;width:210px;text-align:center;vertical-align:middle;margin:5px;padding:3px;border-radius:3px;' >
																
												<div id='".$name_tab[0]."' style='display:none;position:absolute;top:0px; left:190px;' >
													<img src='images/del15.png' onclick=\"delete_bg(".$tab['id'].",'".$name_tab[0]."','".$name_tab[1]."');\"/>
												</div>
												<a href='".$link_high."' target='blank' >
													<img src='".$link_low."' style='margin-top:".((200 - $image_height)/2)."px;' height='".$image_height."' width='".$image_width."' />
												</a>
											</div>"; 
						}
					}
					
					
					?>		
					<div class="wrapper" >
						<div class="grid_1" >&nbsp;</div>
						<div class="grid_10" style="background: url('images/input.png'); margin:1px; font-size:1.3em; height:30px; border-radius:5px;cursor:pointer;" >
							<div class="grid_1" style='text-align:center;padding:5px;' ><?php echo $tab['id']; ?></div>
							<div class="grid_8 one_title normaltip" title="<?php echo $nb_bg; ?>" style="border-radius:15px;width:570px;padding:5px;" onclick="if(bg_selection_<?php echo $tab['id']; ?>) {$('#bg_selection_<?php echo $tab['id']; ?>').slideUp('slow'); bg_selection_<?php echo $tab['id']; ?> = false;} else { $('#bg_selection_<?php echo $tab['id']; ?>').slideDown('slow'); bg_selection_<?php echo $tab['id']; ?> = true;}" >
								<?php echo $tab['keyword']; ?>
							</div>
							<div class="grid_2" style="text-align:right;width:70px;" >
							</div>
						</div>
					</div>
					<div class="wrapper" id="bg_selection_<?php echo $tab['id']; ?>" style="display:none;" >
						<div class="grid_2" >&nbsp;</div>
						<div class="grid_6" >
							<form method="post" class="form" name="bg_form_<?php echo $tab['id']; ?>" enctype="multipart/form-data" >
								<input name='background_<?php echo $tab['id']; ?>' type='file' />
								<input type='hidden' name='background' value='<?php echo $tab['id']; ?>' />
							</form>
						</div>
						<div class="grid_3" >
							<img src='images/submit50.png' onclick='document.bg_form_<?php echo $tab['id']; ?>.submit();' style='border-radius:5px;cursor:pointer;' />
						</div>
						<div class='wrapper' >
							<?php echo $previews; ?>
						</div>
					</div>
					<script type="text/javascript" charset="utf8" >
						var bg_selection_<?php echo $tab['id']; ?> = false;
					</script>
					
					<?php 
				} 
			} ?>
		
		</div>
		
		<div id='keywords' >
			<ul>
				<li><a href='#theme' > Themes </a></li>
				<li><a href='#location' > Locations </a></li>
				<li><a href='#person' > Persons </a></li>
				<li><a href='#genre' > Genres </a></li>
				<li><a href='#misc' > Misc </a></li>
				<li><a href='#system' > Systems </a></li>
			</ul>
			<?php
			display_keywords(KEYWORD_THEME_CAT);
			display_keywords(KEYWORD_LOCATION_CAT);
			display_keywords(KEYWORD_PERSON_CAT);
			display_keywords(KEYWORD_GENRE_CAT);
			display_keywords(KEYWORD_MISC_CAT);
			display_keywords(KEYWORD_SYSTEM_CAT);
			?>
		</div>
		
	</div>
	
	<div id="warning" title="Warning" ></div>
	<style>	.ui-progressbar-value { background-image: url(images/pbar-ani.gif); }	</style>
	<div id="upload" title="Upload" style='display:none;text-align:center;' >
		<div id='bar' style='width:400px;' ></div><br>
		<span id="upload_file" ></span><br>
		<span id="upload_progress" style="font-size:1.7em;" ></span>
	</div>
	
	<script type="text/javascript" charset="utf8" > 
		$('#settings').tabs(); 
		$('#keywords').tabs(); 
	</script>

<?php
	
	include("footer.php");
?>