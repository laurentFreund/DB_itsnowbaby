<?php

################################################################################################
/**********************************************************************************************\
|**		Document:					update_keyword.php                                       **|
|**		Creation:					April 17, 2012                                           **|
|**		Last modification:			June 27, 2012                                            **|
|**		Authors:					Abdoul aziz SENE                                         **|
|**		Project:					It's Now Baby, Industrial project 7, EMSE & LFKs		 **|
|**		Company:					EMSE, Cycle ISMIN, Promo 2013                            **|
|**		Description:				Update keywords in the database                          **|
\**********************************************************************************************/
################################################################################################


include("connexionbdd.php");
Connexion_BDD();

include("config.php");

//exit if variables aren't set
if(!isset($_GET['del'])){
	if(!isset($_GET['id']) ||!isset($_GET['cat']) ||!isset($_GET['val'])){
		echo -1;
		exit();
	}
}
else{
	if(!isset($_GET['id']) ||!isset($_GET['cat'])){
		echo -1;
		exit();
	}
}

switch($_GET['cat']){
	case KEYWORD_THEME_CAT: $field = 'themes'; $cat=2; break;
	case KEYWORD_LOCATION_CAT: $field = 'locations'; $cat=3; break;
	case KEYWORD_PERSON_CAT: $field = 'persons'; $cat=4; break;
	case KEYWORD_MISC_CAT: $field = 'misc'; $cat=5; break;
	case KEYWORD_GENRE_CAT: $field = 'genre'; $cat=6; break;
	case KEYWORD_SYSTEM_CAT: $field = 'system'; $cat=7; break;
	default: $field = ''; $cat=-1; break;
}

//we will need the old keyword for some operations, we get it
$query0 = sprintf("SELECT keyword FROM inb_keywords WHERE id=%d", $_GET['id']);
$request0 = mysql_query($query0);
if(!($request0)){
	echo -2;
	exit();
}
else
	$keyword = mysql_result($request0,0);


//now we delete or update it on keyword table 	
if(!isset($_GET['del'])){
	$query1 = sprintf("UPDATE inb_keywords SET keyword='".$_GET['val']."' WHERE id=%d", $_GET['id']);
	$request1 = mysql_query($query1);
}
else{
	$query1 = sprintf("DELETE FROM inb_keywords WHERE id=%d", $_GET['id']);
	$request1 = mysql_query($query1);
}

//then we must delete or update it in existing medias and key areas using this keyword
if($request1) {
	//for medias
	$query2 = "SELECT ".$field." d, id FROM inb_medias_1 WHERE ".$field." LIKE '%".$keyword."%'";
	$request2 = mysql_query($query2);	//Select medias which may use this keyword
	if($request2) {
		while ($tab=mysql_fetch_assoc($request2)) {	//reading reeesult line by line
			$tab2=explode(',',$tab['d']);	//array of keywords
			
			if(!isset($_GET['del'])){
				if(strlen(''.array_search($keyword, $tab2))>0) $tab2[array_search($keyword, $tab2)] = $_GET['val'];	//update keyword if it exists
			}
			else{
				if(strlen(''.array_search($keyword, $tab2))>0) unset($tab2[array_search($keyword, $tab2)]);	//delete keyword if it exists
			}
			
			$query3 = sprintf("UPDATE inb_medias_1 SET ".$field."='%s' WHERE id=%d", implode(',',$tab2), $tab['id']);	//update medias' keywords
			$request3 = mysql_query($query3);	
			if(!($request3)) {
				echo -4;
				exit();
			}
		}
	}
	else {
		echo -3;
		exit();
	}
	
	//for key areas
	$query4 = "SELECT key_areas k,id FROM inb_medias_1 WHERE key_areas LIKE '%".$keyword."%'";
	$request4 = mysql_query($query4);	///Select texts whose key areas may use this keyword
	if($request4) {
		while ($tab3=mysql_fetch_assoc($request4)) {	//reading reeesult line by line
			$tab4=explode('|',$tab3['k']);	//array of key areas
			for ($i = 0 ; $i<sizeof($tab4) ; $i++) {
				$tab5 = explode('~',$tab4[$i]);	//array of keywords types
				$tab6 = explode(',',$tab5[$cat]);	//array of keywords
				
				if(!isset($_GET['del'])){
					if(strlen(''.array_search($keyword, $tab6))>0) $tab6[array_search($keyword, $tab6)] = $_GET['val'];//update keyword if it exists
				}
				else{
					if(strlen(''.array_search($keyword, $tab6))>0) unset($tab6[array_search($keyword, $tab6)]);	//delete keyword if it exists
				}
				
				$tab5[$cat] =  implode(',',$tab6);
				$tab4[$i] = implode('~',$tab5);
			}
			$query5 = sprintf("UPDATE inb_medias_1 SET key_areas='%s' WHERE id=%d", implode('|',$tab4), $tab3['id']);	//update medias' keywords
			$request5 = mysql_query($query5);	
			if(!($request5)) {
				echo -6;
				exit();
			}
		}
	}
	else {
		echo -5;
		exit();
	}
	
	echo 1;
}
else {
	echo -7;
	exit();
}

?>
