<?php

################################################################################################
/**********************************************************************************************\
|**		Document:					text_add.php                                             **|
|**		Creation:					February 23, 2012                                        **|
|**		Last modification:			June 27, 2012                                            **|
|**		Authors:					Abdoul aziz SENE, Thomas BAUDIN                          **|
|**		Project:					It's Now Baby, Industrial project 7, EMSE & LFKs		 **|
|**		Company:					EMSE, Cycle ISMIN, Promo 2013                            **|
|**		Description:				Text upload page                                         **|
\**********************************************************************************************/
################################################################################################


	$current_menu = 2;//to display 'UPLOAD' menu as selected
	$title = utf8_encode("Text add");//title for the window
	if(isset($_GET['id'])) $title = utf8_encode("Text edit");
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
$success = false;
$successText = "";

//connect to DB
include("inc/connexionbdd.php");
Connexion_BDD();

//load the xml generator function
include("xml/generatexml.php");
//load usefull functions for thematics
include("inc/thematics.functions.php");

//var global pour sequence
$except1 = '';
$except2 = '';

//if a form has been submitted, we register information  
if(isset($_POST['title']))
{
	if(!isset($_POST['id']))//If it's a new media, we check it, move it, convert it, rename it, then add it to the database 
	{
		
		//get information which will be regisreted in inb_medias_1
			//id: automatic
			//length: input name text_length
			//key_areas: input name key_areas
			//mediatype: MEDIATYPE_TEXT
			//title: input name title
			//authors: input name authors
			//themes: input name themes
			//locations: input name locations
			//persons: input name persons
			//misc: input name misc
			//genre: input name genre
			//system: input name system
			//direct links: input name direct_links
			//time
				$time = mysql_real_escape_string(htmlspecialchars($_POST['date_begin']))."~".mysql_real_escape_string(htmlspecialchars($_POST['date_end'])) ;
			//text: input name text_content
			//extract_of: input name extract_of
			//link_type: LINK_TYPE_NO_LINK
			//upload_date: current date given by the sql function curdate()
			//previous: input name previous
			//next: input name next	
			//associated video: input name associated_audio	
			//associated video: input associated_video	
			
		$query1 = sprintf("INSERT INTO inb_medias_1(id,length,key_areas,mediatype,title,authors,themes,locations,persons,misc,genre,system,direct_links,time,text,extract_of,link_type,upload_date,previous,next,associated_audio,associated_video) 
											 VALUES('','%d', \"%s\",      '%d',\"%s\", \"%s\",   \"%s\",  \"%s\",    \"%s\",   \"%s\", \"%s\",    \"%s\",  \"%s\",       \"%s\", \"%s\",  '%d',      '%d',     curdate(),  '%d',    '%d','%d',            '%d'            )",
			mysql_real_escape_string(htmlspecialchars($_POST['text_length'])),
			htmlspecialchars($_POST['key_areas']),
			MEDIATYPE_TEXT,
			htmlspecialchars($_POST['title']),
			htmlspecialchars($_POST['authors']),
			htmlspecialchars($_POST['themes']),
			htmlspecialchars($_POST['locations']),
			htmlspecialchars($_POST['persons']),
			htmlspecialchars($_POST['misc']),
			htmlspecialchars($_POST['genre']),
			htmlspecialchars($_POST['system']),
			mysql_real_escape_string(htmlspecialchars($_POST['direct_links'])),
			$time,
			htmlspecialchars($_POST['text_content']),
			mysql_real_escape_string(htmlspecialchars($_POST['extract_of'])),
			LINK_TYPE_NO_LINK,
			mysql_real_escape_string(htmlspecialchars($_POST['previous'])),
			mysql_real_escape_string(htmlspecialchars($_POST['next'])),
			mysql_real_escape_string(htmlspecialchars($_POST['associated_audio'])),
			mysql_real_escape_string(htmlspecialchars($_POST['associated_video']))
			);
		$request1 = mysql_query($query1);
		
		if($request1)
		{
			/* symetric associated video, associated audio, previous, next: for example if media1 set media2 as next, then media2 will have media1 as previous.  */
			$rows = mysql_fetch_array(mysql_query("SHOW TABLE STATUS LIKE 'inb_medias_1'"));
			$id = $rows['Auto_increment'] -1 ;
			$query_aa = sprintf("UPDATE inb_medias_1 SET associated_text='%d' WHERE id='%d'",$id,mysql_real_escape_string(htmlspecialchars($_POST['associated_audio'])));
			$query_av = sprintf("UPDATE inb_medias_1 SET associated_text='%d' WHERE id='%d'",$id,mysql_real_escape_string(htmlspecialchars($_POST['associated_video'])));
			$query_n = sprintf("UPDATE inb_medias_1 SET next='%d' WHERE id='%d'",$id,mysql_real_escape_string(htmlspecialchars($_POST['previous'])));
			$query_p = sprintf("UPDATE inb_medias_1 SET previous='%d' WHERE id='%d'",$id,mysql_real_escape_string(htmlspecialchars($_POST['next'])));
			
			$request_aa = mysql_query($query_aa);
			$request_av = mysql_query($query_av);
			$request_n = mysql_query($query_n);
			$request_p = mysql_query($query_p);
			/* symetric associated text, previous, next end */
			
			/* symetric direct links: if media1 set media2 as a direct link, then media2 will have media1 as a direct link */
			if(mysql_real_escape_string(htmlspecialchars($_POST['direct_links'])) != ''){
				$tab_dl = explode(',',mysql_real_escape_string(htmlspecialchars($_POST['direct_links'])));
				foreach($tab_dl as $dl){
					$query_dl_1 = sprintf("SELECT direct_links FROM inb_medias_1 WHERE id='%d'",$dl);
					$request_dl_1 = mysql_query($query_dl_1);
					if($request_dl_1){
						$dl_1 = mysql_result($request_dl_1,0);
						if($dl_1 != ''){
							$tab_dl_1 = explode(',',$dl_1);
							if(strlen(''.array_search($id, $tab_dl_1))<=0) 
								$tab_dl_1[] = $id;
							$dl_1 = implode(',',$tab_dl_1);
						}
						else{
							$dl_1 = $id;
						}
						$query_dl_2 = sprintf("UPDATE inb_medias_1 SET direct_links='%s' WHERE id=%d", $dl_1, $dl);
						$request_dl_2 = mysql_query($query_dl_2);
						if(!($request_dl_2)){
							$errorText .= "An error has occurred while processing direct links (level 1, id ".$dl.") into the database: <br />".mysql_error()."<br /> ";
							$error = true;
						}
					}
					else{
						$errorText .= "An error has occurred while processing direct links (level 0) into the database: <br />".mysql_error()."<br /> ";
						$error = true;
					}
				}
			}
			/* symetric direct links end*/
			
			if($request_aa && $request_av && $request_n && $request_p && !($error)){
				add_new_keywords();
				if(!($error)){
					$success = true;
					$successText .= "The text has been registered into the database successfully.";
				}
			}
			else{
				$errorText .= "An error has occurred during the registering (level 2) into the database: <br />".mysql_error()."<br /> ";
				$error = true;
			}
			
		}
		else
		{
			$errorText .= "An error has occurred during the registering (level 1) into the database: <br />".mysql_error()."<br /> ";
			$error = true;
		}
		$xmlgenerated = generatexml();
		if($xmlgenerated < 0){
			$errorText .= "An error has occurred while updating the application: level ".$xmlgenerated.". <br />";
			$error = true;
		}
	}
	else
	{
		$query = sprintf("UPDATE inb_medias_1 SET extract_of='%d',previous='%d',next='%d',associated_audio='%d',associated_video='%d' WHERE id='%d'",
				mysql_real_escape_string(htmlspecialchars($_POST['extract_of'])),
				mysql_real_escape_string(htmlspecialchars($_POST['previous'])),
				mysql_real_escape_string(htmlspecialchars($_POST['next'])),
				mysql_real_escape_string(htmlspecialchars($_POST['associated_audio'])),
				mysql_real_escape_string(htmlspecialchars($_POST['associated_video'])),
				$_POST['id']
				);
		$query_aa = sprintf("UPDATE inb_medias_1 SET associated_text='%d' WHERE id='%d'",$_POST['id'],mysql_real_escape_string(htmlspecialchars($_POST['associated_audio'])));
		$query_av = sprintf("UPDATE inb_medias_1 SET associated_text='%d' WHERE id='%d'",$_POST['id'],mysql_real_escape_string(htmlspecialchars($_POST['associated_video'])));
		$query_n = sprintf("UPDATE inb_medias_1 SET next='%d' WHERE id='%d'",$_POST['id'],mysql_real_escape_string(htmlspecialchars($_POST['previous'])));
		$query_p = sprintf("UPDATE inb_medias_1 SET previous='%d' WHERE id='%d'",$_POST['id'],mysql_real_escape_string(htmlspecialchars($_POST['next'])));
		
		/* symetric direct links: if media1 set media2 as a direct link, then media2 will have media1 as a direct link */
		if(mysql_real_escape_string(htmlspecialchars($_POST['direct_links'])) != ''){
			$tab_dl = explode(',',mysql_real_escape_string(htmlspecialchars($_POST['direct_links'])));
			foreach($tab_dl as $dl){
				$query_dl_1 = sprintf("SELECT direct_links FROM inb_medias_1 WHERE id='%d'",$dl);
				$request_dl_1 = mysql_query($query_dl_1);
				if($request_dl_1){
					$dl_1 = mysql_result($request_dl_1,0);
					if($dl_1 != ''){
						$tab_dl_1 = explode(',',$dl_1);
						if(strlen(''.array_search($_POST['id'], $tab_dl_1))<=0) 
							$tab_dl_1[] = $_POST['id'];
						$dl_1 = implode(',',$tab_dl_1);
					}
					else{
						$dl_1 = $_POST['id'];
					}
					$query_dl_2 = sprintf("UPDATE inb_medias_1 SET direct_links='%s' WHERE id=%d", $dl_1, $dl);
					$request_dl_2 = mysql_query($query_dl_2);
					if(!($request_dl_2)){
						$errorText .= "An error has occurred while processing direct links (level 1, id ".$dl.") into the database: <br />".mysql_error()."<br /> ";
						$error = true;
					}
				}
				else{
					$errorText .= "An error has occurred while processing direct links (level 0) into the database: <br />".mysql_error()."<br /> ";
					$error = true;
				}
			}
		}
		/* symetric direct links end*/
		
		$request_aa = mysql_query($query_aa);
		$request_av = mysql_query($query_av);
		$request_n = mysql_query($query_n);
		$request_p = mysql_query($query_p);
		$request = mysql_query($query);
		
		if(!($request) ||!($request_aa) ||!($request_av) ||!($request_n) ||!($request_p) || $error){
			$errorText .= "An error has occurred during the updating of the text (level 1.0) into the database: <br />".mysql_error()."<br /> ";
			$error = true;
		}
		update_media('text');
	}

}

if(isset($_GET['id'])){// if we are modifying a media, we get all the metadata from the database
	$query = sprintf("SELECT * FROM inb_medias_1 WHERE mediatype=0 AND link_type=0 AND id='%d'",$_GET['id']);
	$request = mysql_query($query);
	if(!($request))
	{
		$errorText .= "An error has occurred during the information gathering (level 0) from the database: <br />".mysql_error()."<br /> ";
		$error = true;
	}
	$tab = mysql_fetch_array($request); 
}


	display_notification_box_and_title('a text');
	?>	
	
	<form action="text_add.php" method="post" class="form" id="add_form" name="add_form" >
		
		<?php
		display_head_metadata('text');// title, authors, date
		display_media_add_fields('text');// textarea 
		display_thematic_metadata(1);//key-areas, keywords and direct links
		?>
		
	</form>
	
	<div id="warning" title="Warning" ></div>
	
	<?php 
	echo_options_add_value_init('text'); //initialize some usefull variables
	echo_submit_function('','');//the javascript fonction for the submit
	?>	

	<?php if(isset($_GET['id'])) { //if are modifying a text
		?>
		<script type="text/javascript" charset="utf8" >
			var tab_direct_link = explode(',',ajax_open("inc/options.php?cat=-1&except=<?php echo $_GET['id']; ?>"));
			tab_direct_link = [0].concat(tab_direct_link);
			var tab_keywords = [tab_theme,tab_location,tab_person,tab_genre,tab_misc,tab_system,tab_direct_link];
		</script>
		
		<script type="text/javascript" charset="utf8" >
			var tab2 = [] ; var tab3 = [] ; var tab4 = [] ; var tab5 = [] ;var tab6 = [] ; var tab7 = [] ; var tab8 = [] ;
			var tab_2 = [] ; var tab_3 = [] ; var tab_4 = [] ; var tab_5 = [] ; var tab_6 = [] ; var tab_7 = [] ; var tab_8 = [] ;
		</script>
		<?php 
		// we load all key-areas
		if ($tab['key_areas']!='') { //if the keyareas list isn't empty, we can start load them the javascript variables
			$tab_of_clic_areas = explode('|',$tab['key_areas']);//transform the list(string) into an array
			foreach($tab_of_clic_areas as $one_area){
				if(($one_area != '')){//each key area is codifued like this:
									//begin~end~theme~location~person~genre~misc~system
									// begin and end are for the js array named tab_of_areas_position
									// the others are for the js array named tab_of_areas
				?>	
					<script type="text/javascript" charset="utf8" >
					tab2 = [] ; tab3 = [] ; tab4 = [] ; tab5 = [] ; tab6 = [] ; tab7 = [] ; tab8 = [] ; 
					tab_2 = [] ; tab_3 = [] ; tab_4 = [] ; tab_5 = [] ; tab_6 = [] ; tab_7 = [] ; tab_8 = [] ; <?php 
					$element = explode('~',$one_area); //transform the elements(string) into an array
					// begin and end are for the js array named tab_of_areas_position
					if($element[0] != '' && $element[1] != '') { ?> tab_of_areas_position.push([<?php echo $element[0].','.$element[1]; ?>]); <?php }
					
					// the others are for the js array named tab_of_areas
					for($i = 2 ; $i <= 8 ; $i++){
						if($element[$i] != ''){
							$tab2 = explode(',',$element[$i]);
							foreach($tab2 as $one){
								if($one != '') { ?> tab<?php echo $i; ?>.push('<?php echo $one; ?>'); 
								<?php }
							}
							?>
							for(var i = 0 ; i < tab_keywords[<?php echo ($i-2); ?>].length ; i++){
								if(array_search(tab_keywords[<?php echo ($i-2); ?>][i],tab<?php echo $i; ?>)>=0)	tab_<?php echo $i; ?>.push('1');
								else tab_<?php echo $i; ?>.push('0');			
							}<?php
						}	
					}
					?>tab_of_areas.push([tab_2, tab_3, tab_4, tab_5, tab_6, tab_7, tab_8]);
					</script>
					<?php
				}
			}
		}
		
		// we color key areas into blue
		?>
		<script type="text/javascript" charset="utf8" >
			
			text="<?php echo html_entity_decode($tab['text']); ?>";//original text 
			text_visible = text;//text which will be colored thanks to html tags
			var position = [];//an array where will register the colored key areas
			for (var i = 0 ; i < tab_of_areas_position.length ; i++){//for any key-area
				var begin = tab_of_areas_position[i][0];
				var end = tab_of_areas_position[i][1];
				//we search for the shifted begin:
				//as we color the text usint html tags, when we add them to the original text, 
				//the positions according to that text can become wrong. So we figure out the begin position
				//(shifted begin) according to the visible text, shifting with tags length
				var shifted_begin = shift(begin,position) + begin; 
				position.push(tab_of_areas_position[i]);//we this keyarea, so on next loop the shift calculation will consider it 
				//now, we have the shifted begin, we can add tags arround the key area in order to color it
				text_visible = text_visible.substr(0, shifted_begin) + tag_color + text_visible.substr(shifted_begin,end-begin) + close_tag + text_visible.substr(shifted_begin+end-begin);
				$("#added_text").html(text_visible);
			}
			
			clic_areas_nb = tab_of_areas_position.length - 1;//the key area number start at 0
			edit_text = false;//the text wont be edited
			
		</script>
		
		
		<?php
		load_principal_keywords('text');//pre-select keywords according to the metadata of the media we are modifying 
	}
	

	
	include("footer.php");
?>


