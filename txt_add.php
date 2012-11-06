<?php

################################################################################################
/**********************************************************************************************\
|**		Document:					txt_add.php                                              **|
|**		Creation:					June 26, 2012                                            **|
|**		Last modification:			June 27, 2012                                            **|
|**		Authors:					Abdoul aziz SENE                                         **|
|**		Project:					It's Now Baby, Industrial project 7, EMSE & LFKs		 **|
|**		Company:					EMSE, Cycle ISMIN, Promo 2013                            **|
|**		Description:				txt files upload and processing                          **|
\**********************************************************************************************/
################################################################################################


	$current_menu = 2;//to display 'UPLOAD' menu as selected
	$title = utf8_encode("TXT file add");//title for the window
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
	if($_FILES['thefile']['error']  !== UPLOAD_ERR_OK){//if there was error during uploading
		$errorText .= "There is no file to upload.<br /> ";
		$error = true;
	}
	else if (is_uploaded_file($_FILES['thefile']['tmp_name'])) //else if the upload was successfull
	{
		$nameOriginal = $_FILES['thefile']['name'];//te file's name
		$elementsWay = pathinfo($nameOriginal);//the file's path
		$extensionFile = strtolower($elementsWay['extension']);//the file's extention
		$allowedExtensions = array('txt');// the list of allowed extensions for the cenverter or the application
		if(in_array($extensionFile,$allowedExtensions))// if the file's extention is OK, we can add it to our database
		{
			
			$full_line = '';
			
			
			if($file_array = file($_FILES['thefile']['tmp_name']))
			{
				foreach($file_array as $line)//getting all file lines
				{
					$line = rtrim($line);
					$full_line .= $line.'<br>';
				}	
				
				$full_line = str_replace('"', "`", $full_line);
				//get information to put in the database
					//id : auto
					//title : $title
					//authors: input name authors
					//time
						$time = mysql_real_escape_string(htmlspecialchars($_POST['date_begin']))."~".mysql_real_escape_string(htmlspecialchars($_POST['date_end'])) ;
					//mediatype : MEDIATYPE_TEXT
					//link_type : LINK_TYPE_NO_LINK
					//text : $full_line
					//length : strlen($full_line)
					
				$query1 = sprintf("INSERT INTO inb_medias_1(id,length,mediatype,title,authors, time, text, themes, locations, persons, misc, genre, system, link_type, upload_date) 
													VALUES('', '%d',  '%d',     \"%s\",'%s'  , '%s', \"%s\", \"%s\",  \"%s\",    \"%s\",  \"%s\", \"%s\",\"%s\",'%d',   curdate()  )",
											strlen($full_line),
											MEDIATYPE_TEXT,
											htmlspecialchars($_POST['title']),
											htmlspecialchars($_POST['authors']),
											$time,
											$full_line,
											htmlspecialchars($_POST['themes']),
											htmlspecialchars($_POST['locations']),
											htmlspecialchars($_POST['persons']),
											htmlspecialchars($_POST['misc']),
											htmlspecialchars($_POST['genre']),
											htmlspecialchars($_POST['system']),
											mysql_real_escape_string(htmlspecialchars($_POST['direct_links'])),
											LINK_TYPE_NO_LINK
											);
				$request1 = mysql_query($query1);
				if(!$request1)
				{
					$errorText .= "An error has occurred <br />".mysql_error()."<br /> ";
					$error = true;
				}
				
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
			}
			
			
			if(!$error){
				add_new_keywords();
				if(!($error)){
					$success = true;
					$successText = "The txt file processing was successfull.";
				}
			}
		}
		else
		{
			$errorText .= "'".$_FILES['thefile']['name']."' is an invalide file type. Please choose one of these file types: txt<br /> ";
			$error = true;
		}
	} 
	else
	{
		$errorText .=  "An error has occurred during the file upload<br />";
		$error = true;
	}
	
	$xmlgenerated = generatexml();//update the XML file
	if($xmlgenerated < 0){
		$errorText .= "An error has occurred while updating the application: level ".$xmlgenerated.". <br />";
		$error = true;
	}
}

	display_notification_box_and_title('a TXT file');
	?>
	
	<form action="txt_add.php" method="post" class="form" id="add_form" name="add_form" enctype="multipart/form-data" >
	
		<?php
		display_head_metadata('txt');
		display_media_add_fields('txt');
		display_thematic_metadata(0);
		?>
		
	
	</form>
	<div class='wrapper' ><div class='grid_2' >
	<div id="warning" title="Warning" ></div>
	<style>	.ui-progressbar-value { background-image: url(images/pbar-ani.gif); }	</style>
	<div id="upload" title="Upload" style='display:none;text-align:center;' >
		<div id='bar' style='width:400px;' ></div><br>
		<span id="upload_file" ></span><br>
		<span id="upload_progress" style="font-size:1.7em;" ></span>
	</div>
	
	<?php 
	echo_options_add_value_init('txt'); //initialize some usefull variables
	echo_submit_function('Data processing',"'txt'");//the javascript fonction for the submit
	?>
<?php	
	include("footer.php");
?>