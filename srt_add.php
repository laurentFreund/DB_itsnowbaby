<?php

################################################################################################
/**********************************************************************************************\
|**		Document:					srt_add.php                                              **|
|**		Creation:					May 11, 2012                                             **|
|**		Last modification:			June 27, 2012                                            **|
|**		Authors:					Abdoul aziz SENE                                         **|
|**		Project:					It's Now Baby, Industrial project 7, EMSE & LFKs		 **|
|**		Company:					EMSE, Cycle ISMIN, Promo 2013                            **|
|**		Description:				str files upload and processing                          **|
\**********************************************************************************************/
################################################################################################


	$current_menu = 2;//to display 'UPLOAD' menu as selected 
	$title = utf8_encode("SRT file add");//title for the window
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
		$allowedExtensions = array('srt');// the list of allowed extensions for the cenverter or the application
		if(in_array($extensionFile,$allowedExtensions))// if the file's extention is OK, we can add it to our database
		{
			
			$full_line = '';
			$ids_array = array();
			
			if($_POST['srt_type'] > 0){
				if($file_array = file($_FILES['thefile']['tmp_name']))
				{
					$sub_number = 0;
					$part = 1;
					foreach($file_array as $line)
					{
						$line = rtrim($line);
						if($line == '')
							$sub_number++;
					}
					foreach($file_array as $line)
					{
						$line = rtrim($line);

						// get begin and end
						//                00  :  00  :  32  ,   000   -->   00  :  00  :  37  ,   000
						if(preg_match('/(\d\d):(\d\d):(\d\d),(\d\d\d) --> (\d\d):(\d\d):(\d\d),(\d\d\d)/', $line, $match))
						{
							$line_begin = intval($match[1])*3600 +  intval($match[2])*60 + intval($match[3]);
							$line_end   = intval($match[5])*3600 +  intval($match[6])*60 + intval($match[7]);
							$full_line = '';
						}
						// if the next line is not blank, get the text
						elseif($line != '')
						{
							$full_line .= '<br>'.$line;
						}

						// if the next line is blank, write the paragraph
						if($line == '')
						{
						
							$full_line = str_replace('"', "`", $full_line);
							$full_line = str_replace('¨', "`", $full_line);
							//get information to put in the database
								//id : auto
								//title : $title.' '.$part
								//authors: input name authors
								//time
									$time = mysql_real_escape_string(htmlspecialchars($_POST['date_begin']))."~".mysql_real_escape_string(htmlspecialchars($_POST['date_end'])) ;
								//mediatype : MEDIATYPE_TEXT
								//link_type : LINK_TYPE_NO_LINK
								//text : $full_line
								//length : strlen($full_line)
								
							$query1 = sprintf("INSERT INTO inb_medias_1(id,length,mediatype,title,authors, time, text,time_end, themes, locations, persons, misc, genre, system, link_type,  upload_date) 
																VALUES('', '%d',  '%d',     \"%s\",\"%s\", '%s', \"%s\",'%d',    \"%s\",  \"%s\", \"%s\",  \"%s\", \"%s\",\"%s\", '%d',    curdate()  )",
														strlen($full_line),
														MEDIATYPE_TEXT,
														mysql_real_escape_string(htmlspecialchars($_POST['title']))." ".$part."/".$sub_number,
														mysql_real_escape_string(htmlspecialchars($_POST['authors'])),
														$time,
														$full_line,
														($line_end-$line_begin),
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
							else{
							
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
			
								
								$rows = mysql_fetch_array(mysql_query("SHOW TABLE STATUS LIKE 'inb_medias_1'"));//the table's metadata
								$id = $rows['Auto_increment'] - 1;// the id of the text we just register
								$ids_array[$part] = $id;
								$part++;
							}
						}
					}
				}
			}
			if($_POST['srt_type'] == 0 || $_POST['srt_type'] == 2){
				if($file_array = file($_FILES['thefile']['tmp_name']))
				{
					$part = 1;
					$temp = '';
					foreach($file_array as $line)
					{
						$line = rtrim($line);

						// get begin and end
						//                00  :  00  :  32  ,   000   -->   00  :  00  :  37  ,   000
						if(preg_match('/(\d\d):(\d\d):(\d\d),(\d\d\d) --> (\d\d):(\d\d):(\d\d),(\d\d\d)/', $line, $match))
						{
							$line_begin = intval($match[1])*3600 +  intval($match[2])*60 + intval($match[3]);
							$line_end   = intval($match[5])*3600 +  intval($match[6])*60 + intval($match[7]);
							$full_line = '';
						}
						// if the next line is not blank, get the text
						elseif($line != '')
						{
							$full_line .= ' '.$line;
						}

						// if the next line is blank, write the paragraph
						if($line == '')
						{
							if($_POST['srt_type'] == 0){
								$full_line = str_replace('~', "-", $full_line);
								$full_line = str_replace('|', "/", $full_line);
								$full_line = str_replace('"', "`", $full_line);
								$temp .= $line_begin.'~'.$line_end .'~0~'.htmlspecialchars($full_line).'|' ;
							}
							if($_POST['srt_type'] == 2)
								$temp .= $line_begin.'~'.$line_end .'~1~'.$ids_array[$part].'|' ;
							
							$part++;
						}
					}
					$temp = substr($temp,0,-1);
					if($_POST['audio'] != 0){
						$query1 = sprintf("UPDATE inb_medias_1 SET subtitles='".$temp."' WHERE id=".$_POST['audio']);
						$request1 = mysql_query($query1);
						if(!$request1)
						{
							$errorText .= "An error has occurred while updating audio subtitles<br />".mysql_error()."<br /> ";
							$error = true;
						}
					}
					if($_POST['video'] != 0){
						$query1 = sprintf("UPDATE inb_medias_1 SET subtitles='".$temp."' WHERE id=".$_POST['video']);
						$request1 = mysql_query($query1);
						if(!$request1)
						{
							$errorText .= "An error has occurred while updating audio subtitles<br />".mysql_error()."<br /> ";
							$error = true;
						}
					}
				}
			}
			if(!$error){
				add_new_keywords();
				if(!($error)){
					$success = true;
					$successText = "The srt file processing was successfull.";
				}
			}
		}
		else
		{
			$errorText .= "'".$_FILES['thefile']['name']."' is an invalide file type. Please choose one of these file types: srt <br /> ";
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

	display_notification_box_and_title('a SRT file');
	?>
	
	<form action="srt_add.php" method="post" class="form" id="add_form" name="add_form" enctype="multipart/form-data" >
	
		<?php
		display_head_metadata('srt');
		display_media_add_fields('srt');
		display_thematic_metadata(0);
		?>
		
		<!-- these two input a here to fix a weird problem due to srt files processing -->
		<input type='hidden' name='audio' id='audio' />
		<input type='hidden' name='video' id='video' />
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
	echo_options_add_value_init('srt'); //initialize some usefull variables
	echo_submit_function('Data processing',"'srt'");//the javascript fonction for the submit
	?>

	<script charset='utf-8' type='text/javascript' >
		$('#srt_type_div').html("<div class='wrapper'><div class='grid_1' style='margin:3px;' ><input type='radio' name='srt_type' id='srt_type_text' value='1' onchange='ask_audio();' checked /><label style='min-height:30px;height:30px;' for='srt_type_text' >Texts</label></div><div class='grid_2' style='margin:3px;' ><input type='radio' name='srt_type' id='srt_type_sub' value='0' onchange='ask_audio();' /><label style='min-height:30px;height:30px;' for='srt_type_sub'>Subtitles</label></div><div class='grid_1' style='margin:3px;' ><input type='radio' name='srt_type' id='srt_type_both' value='2' onchange='ask_audio();' /><label style='min-height:30px;height:30px;' for='srt_type_both'>Both</label></div></div><br><div id='associated_audio' ></div>");
		$('#srt_type_div').buttonset();
		
		function ask_audio(){
			if($('#srt_type_sub').attr('checked') == 'checked' || $('#srt_type_both').attr('checked') == 'checked'){
				$('#associated_audio').html($('#needed_for_srt').html());
				$('#associated_audio').slideDown();
			}
			else{
				$('#associated_audio').slideUp(function(){
					$('#associated_audio').html('');
				});
			}
		}
	
	</script>
<?php	
	include("footer.php");
?>