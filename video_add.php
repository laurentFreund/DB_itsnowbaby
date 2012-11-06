<?php

################################################################################################
/**********************************************************************************************\
|**		Document:					video_add.php                                            **|
|**		Creation:					March 19, 2012                                           **|
|**		Last modification:			June 27, 2012                                            **|
|**		Authors:					Abdoul aziz SENE                                         **|
|**		Project:					It's Now Baby, Industrial project 7, EMSE & LFKs		 **|
|**		Company:					EMSE, Cycle ISMIN, Promo 2013                            **|
|**		Description:				Videos upload pages                                      **|
\**********************************************************************************************/
################################################################################################


	$current_menu = 2;//to display 'UPLOAD' menu as selected
	$title = utf8_encode("Video add");//title for the window 
	if(isset($_GET['id'])) $title = utf8_encode("Video edit");
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



//if a form has been submitted, we register information  
if(isset($_POST['title']))
{	
	if(!isset($_POST['id']))//If it's a new media, we check it, move it, convert it, rename it, then add it to the database 
	{
		if($_FILES['thefile']['error']  !== UPLOAD_ERR_OK){//if there was error during uploading
			$errorText .= "There is no file to upload.<br /> ";
			$error = true;
		}
		else if (is_uploaded_file($_FILES['thefile']['tmp_name'])) //else if the upload was successfull
		{
			$nameOriginal = $_FILES['thefile']['name'];//te file's name
			$elementsWay = pathinfo($nameOriginal);//the file's path
			$extensionFile = strtolower($elementsWay['extension']);//the file's extention
			$allowedExtensions = array('mpg','mpeg','wmv','vcd','ogg','rm','rv','ogv','nuv','mp4','mov','mjpeg ','m4v','3g2','3gp','4xm','iff','mtv','a64','ac3','asf','avi','avm2','avs','bink','dv','dvd','flv','h261','h262','h264','iv8','ipod'); 
			if(in_array($extensionFile,$allowedExtensions))// if the file's extention is OK, we can add it to our database
			{
				// we load the video
					$movie = new ffmpeg_movie($_FILES['thefile']['tmp_name']);
				$directoryDestination = dirname(__FILE__)."/";//the current path
				$rows = mysql_fetch_array(mysql_query("SHOW TABLE STATUS LIKE 'inb_medias_1'"));//the table's metadata
				$id = $rows['Auto_increment'];// the auto-increment value
				$dirty_title= implode('_',explode(' ',mysql_real_escape_string(htmlspecialchars($_POST['title']))));// we replace spaces by underscores
				$forbidden = array("\\", "/", "|", ":", "*", "?", "&lt;", "&gt;");// forbidden chars list
				$title = str_replace($forbidden, "", $dirty_title);//this is the new title of the file without the forbidden chars
				$nameDestination = $id."_".$title.".".$movie->getFrameWidth()."x".$movie->getFrameHeight().".".$extensionFile;//new file's name 
				$relative_link = "medias/videos/".$nameDestination ;// new file's name in his path
				$link = $directoryDestination.$relative_link ;// root path of the file
				if(!move_uploaded_file($_FILES['thefile']['tmp_name'] , $link)){//we move the file to his new path
					echo "An error has occurred during the file move<br />";
				}
				else//then we can add the media to the database
				{
					//convert
					if($extensionFile != 'ogv'){
						$unlink = true;
						/* convert */
						system(escapeshellcmd("ffmpeg2theora ".$relative_link), $retval);
					}
					else{
						$retval = 0;
						$unlink = false;
					}
					if($retval == 0){
						if($unlink) unlink($relative_link);
					
						
						//get information which will be regisreted in inb_medias_1
							//id: automatic
							//mediatype: MEDIATYPE_VIDEO
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
							//upload_date: current date given by the sql function curdate()
							//link_type: LINK_TYPE_RELATIVE
							//link: relative link
							//weight: post files size
							//extenseion: entensionFile
							//length: $movie->getDuration()
							//width: $movie->getFrameWidth()
							//height:$movie->getFrameHeight()
							//associated_text: input associated_text
							
				
						$query1 = sprintf("INSERT INTO inb_medias_1(id,mediatype,title,authors,themes,locations,persons,misc,genre,system,direct_links,time,upload_date,link_type,link,weight,extension,length,width,height,associated_text) 
															 VALUES('','%d',\"%s\", \"%s\",   \"%s\",  \"%s\",    \"%s\",   \"%s\", \"%s\",    \"%s\",  \"%s\",       \"%s\", curdate(),  '%d',     \"%s\",  '%d',  \"%s\",     '%d' , '%d' ,'%d',  '%d'           )",
							MEDIATYPE_VIDEO,
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
							LINK_TYPE_RELATIVE,
							"medias/videos/".$id."_".$title.".".$movie->getFrameWidth()."x".$movie->getFrameHeight().".ogv",
							$_FILES['thefile']['size'],
							"ogv",
							$movie->getDuration(),
							$movie->getFrameWidth(),
							$movie->getFrameHeight(),
							mysql_real_escape_string(htmlspecialchars($_POST['associated_text']))
							);
						$request1 = mysql_query($query1);
						
						if($request1)
						{
							/* symetric associated text: if video1 set text2 as associated text, then text2 will have video1 as associated audio */
							$query_av = sprintf("UPDATE inb_medias_1 SET associated_video='%d' WHERE id='%d'",$id,mysql_real_escape_string(htmlspecialchars($_POST['associated_text'])));
							$request_av = mysql_query($query_av);
							/* symetric associated text end */
							
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
							
							if($request_av && !($error)){
								add_new_keywords();
								if(!($error)){
									$success = true;
									$successText .= "The video has been registered into the database successfully.";
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
					}
					else{
						$errorText .= "An error has occurred while converting to ogg/theora : ".$retval."<br /> ";
						$error = true;
					}
				}
			}
			else
			{
				$errorText .= "'".$_FILES['thefile']['name']."' is an invalide file type. Please choose one of these file types: ".implode(', ',$allowedExtensions)." etc.<br /> ";
				$error = true;
			}
		} 
		else
		{
			$errorText .=  "An error has occurred during the file upload<br />";
			$error = true;
		}
	}
	else// this isn't a new media, one was edited 
	{
		$query = sprintf("UPDATE inb_medias_1 SET associated_text='%d' WHERE id='%d'",
				mysql_real_escape_string(htmlspecialchars($_POST['associated_text'])),
				$_POST['id']
				);
		$query_av = sprintf("UPDATE inb_medias_1 SET associated_video='%d' WHERE id='%d'",$_POST['id'],mysql_real_escape_string(htmlspecialchars($_POST['associated_text'])));
		
		$request_av = mysql_query($query_av);
		$request = mysql_query($query);
		
		
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
		
		if(!($request) || !($request_av) || $error){
			$errorText .= "An error has occurred during the updating of the video (level 1.0) into the database: <br />".mysql_error()."<br /> ";
			$error = true;
		}
		update_media('video');
	}
	$xmlgenerated = generatexml();//update the XML file
	if($xmlgenerated < 0){
		$errorText .= "An error has occurred while updating the application: level ".$xmlgenerated.". <br />";
		$error = true;
	}
}

if(isset($_GET['id'])){// if we are modifying a media, we get all the metadata from the database
	$query = sprintf("SELECT * FROM inb_medias_1 WHERE mediatype=3 AND link_type=1 AND id='%d'",$_GET['id']);
	$request = mysql_query($query);
	if(!($request))
	{
		$errorText .= "An error has occurred during the information gathering (level 0) from the database: <br />".mysql_error()."<br /> ";
		$error = true;
	}
	$tab = mysql_fetch_array($request); 
}

	display_notification_box_and_title('a video');
	?>
	
	<form action="video_add.php" method="post" class="form" id="add_form" name="add_form" enctype="multipart/form-data" >
	
		<?php
		display_head_metadata('video');
		display_media_add_fields('video');
		display_thematic_metadata(0);
		?>
		
	</form>
	
	<div id="warning" title="Warning" ></div>
	<style>	.ui-progressbar-value { background-image: url(images/pbar-ani.gif); }	</style>
	<div id="upload" title="Upload" style='display:none;text-align:center;' >
		<div id='bar' style='width:400px;' ></div><br>
		<span id="upload_file" ></span><br>
		<span id="upload_progress" style="font-size:1.7em;" ></span>
	</div>
	
	<?php 
	echo_options_add_value_init('video'); //initialize some usefull variables
	echo_submit_function('Converting to ogg/theora ...',"'mpg','mpeg','wmv','vcd','ogg','rm','rv','ogv','nuv','mp4','mov','mjpeg ','m4v','3g2','3gp','4xm','iff','mtv','a64','ac3','asf','avi','avm2','avs','bink','dv','dvd','flv','h261','h262','h264','iv8','ipod'");
	?>	

<?php if(isset($_GET['id'])) { //if are modifying a video
		?>
		
		<script type="text/javascript" charset="utf8" >
			var tab_direct_link = explode(',',ajax_open("inc/options.php?cat=-1&except=<?php echo $_GET['id']; ?>"));
			tab_direct_link = [0].concat(tab_direct_link);
			var tab_keywords = [tab_theme,tab_location,tab_person,tab_genre,tab_misc,tab_system,tab_direct_link];
		</script>
		
		<script type="text/javascript" charset="utf8" >		
			edit = true;
		</script>
		
		<?php
		load_principal_keywords();//pre-select keyword according to the metadata of the media we are modifying 
	}
	
	include("footer.php");
?>