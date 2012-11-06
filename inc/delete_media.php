<?php

################################################################################################
/**********************************************************************************************\
|**		Document:					delete_media.php                                         **|
|**		Creation:					March 15, 2012                                           **|
|**		Last modification:			June 27, 2012                                            **|
|**		Authors:					Abdoul aziz SENE, Thomas BAUDIN                          **|
|**		Project:					It's Now Baby, Industrial project 7, EMSE & LFKs		 **|
|**		Company:					EMSE, Cycle ISMIN, Promo 2013                            **|
|**		Description:				Delete a media from the database                         **|
\**********************************************************************************************/
################################################################################################
//connect to DB 
include("connexionbdd.php");
Connexion_BDD();

//load config file
include("config.php");

//load the xml generator function
include("../xml/generatexml.php");

//exit if variables aren't set
if(!isset($_GET['id'])) {echo -1; exit();}  
$id = $_GET['id'];

//get the media metadata
$query0 = sprintf("SELECT * FROM inb_medias_1 WHERE id=%d", $id);
$request0 = mysql_query($query0);
$media = mysql_fetch_array($request0);

//delete the media from the DB
$query1 = sprintf("DELETE FROM inb_medias_1 WHERE id=%d", $id);
$request1 = mysql_query($query1);
if($request1) {
	//Sélect the medias which have this medias as direct link
	$query2 = "SELECT direct_links d, id FROM inb_medias_1 WHERE direct_links LIKE '%".$id."%'";
	$request2 = mysql_query($query2);	
	if($request2) {
		while ($tab=mysql_fetch_assoc($request2)) {	//reading results line by line
			$tab2=explode(',',$tab['d']);	//array of direct links
			unset($tab2[array_search($_GET['id'], $tab2)]);	//unset direct_link if it exists
			$query3 = sprintf("UPDATE inb_medias_1 SET direct_links='%s' WHERE id=%d", implode(',',$tab2), $tab['id']);	//update the field
			$request3 = mysql_query($query3);	
			if(!($request3)) {
				echo "-3";
				exit();
			}
		}
		
		//if a file must be deleted
		if(isset($_GET['thefile'])) {
			//if it is an image, we must delete 3 other files (low, medium, high)
			if($media['mediatype'] == MEDIATYPE_IMAGE){
				if(!unlink("../medias/images/low/".$_GET['thefile'])) {
					echo "-11";
					exit();
				}
				if(!unlink("../medias/images/medium/".$_GET['thefile'])) {
					echo "-12";
					exit();
				}
				if(!unlink("../medias/images/high/".$_GET['thefile'])) {
					echo "-13";
					exit();
				}
				if(!unlink("../medias/images/original/".$_GET['thefile'])) {
					echo "-13";
					exit();
				}
			}
			else{
				if(!unlink("../".$_GET['thefile'])) {//we delete it
					echo "-10";
					exit();
				}
			}
		}
		
		//if this media had a previous, we must unset the 'next' field of the latest 
		if($media['previous'] != 0){
			$queryp = sprintf("UPDATE inb_medias_1 SET next=0 WHERE next=%d", $id);	
			$requestp = mysql_query($queryp);	
			if(!($requestp)) {
				echo "-30";
				exit();
			}
		}
		//if this media had a next, we must unset the 'previous' field of the latest 
		if($media['next'] != 0){
			$queryn = sprintf("UPDATE inb_medias_1 SET previous=0 WHERE previous=%d", $id);	
			$requestn = mysql_query($queryn);	
			if(!($requestn)) {
				echo "-31";
				exit();
			}
		}
		//if this media had an associated text (it is a video or an audio track), we must unset the 'associated_audio' or 'associated_video' field of the latest 
		if($media['associated_text'] != 0){
			$queryat = sprintf("UPDATE inb_medias_1 SET associated_audio=0, associated_video=0 WHERE associated_audio=%d OR associated_video=%d", $id, $id);	
			$requestat = mysql_query($queryat);	
			if(!($requestat)) {
				echo "-32";
				exit();
			}
		}
		//if this media had an associated audio or associated video (it is a text), we must unset the 'associated_text' field of the latest
		if($media['associated_audio'] != 0 || $media['associated_video'] != 0){
			$queryav = sprintf("UPDATE inb_medias_1 SET associated_text=0 WHERE associated_text=%d", $id);	
			$requestav = mysql_query($queryav);	
			if(!($requestav)) {
				echo "-33";
				exit();
			}
		}
		//if a media is extracted form an archive (this media), we must unset the 'extract_of' field of the latest
		if($media['mediatype'] == MEDIATYPE_TEXT && $media['link_type'] == LINK_TYPE_RELATIVE){
			$queryar = sprintf("UPDATE inb_medias_1 SET extract_of=0 WHERE extract_of=%d", $id);	
			$requestar = mysql_query($queryar);	
			if(!($requestar)) {
				echo "-34";
				exit();
			}
		}
	}
	else {
		echo "-2";
		exit();
	}
	
	//Sélect the medias which have this medias as direct link of key area
	$query4 = "SELECT key_areas k,id FROM inb_medias_1 WHERE key_areas LIKE '%".$_GET['id']."%'";
	$request4 = mysql_query($query4);
	if($request4) {
		while ($tab3=mysql_fetch_assoc($request4)) {
			$tab4=explode('|',$tab3['k']);	//array of key areas
			for ($i = 0 ; $i<sizeof($tab4) ; $i++) {
				$tab5 = explode('~',$tab4[$i]);	//array of keywords
				$tab6 = explode(',',$tab5[8]);	//array of direct link
				if(strlen(''.array_search($_GET['id'], $tab6))>0) unset($tab6[array_search($_GET['id'], $tab6)]);	//unset the direct link if it exists
				$tab5[8] =  implode(',',$tab6);
				$tab4[$i] = implode('~',$tab5);
			}
			$query5 = sprintf("UPDATE inb_medias_1 SET key_areas='%s' WHERE id=%d", implode('|',$tab4), $tab3['id']);	//update of the key areas
			$request5 = mysql_query($query5);	
			if(!($request5)) {
				echo "-5";
				exit();
			}
		}
	
		echo "1";
	}
	else {
		echo "-4";
		exit();
	}
	
}
else {
	echo "-9";
	exit();
}

?>
