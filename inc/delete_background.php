<?php

################################################################################################
/**********************************************************************************************\
|**		Document:					delete_background.php                                    **|
|**		Creation:					June 01, 2012                                            **|
|**		Last modification:			June 27, 2012                                            **|
|**		Authors:					Abdoul aziz SENE, Thomas BAUDIN                          **|
|**		Project:					It's Now Baby, Industrial project 7, EMSE & LFKs		 **|
|**		Company:					EMSE, Cycle ISMIN, Promo 2013                            **|
|**		Description:				Delete a background from the database                    **|
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
if(!isset($_GET['id']) || !isset($_GET['name']) ) {echo -1; exit();} 
extract($_GET);

//get the string list of backgrounds
$query0 = sprintf("SELECT background FROM inb_keywords WHERE id=%d", $id);
$request0 = mysql_query($query0);
$backgrounds = mysql_result($request0, 0);


$tab=explode(',',$backgrounds);	//array of backgrounds
unset($tab[array_search($name.'.png', $tab)]);	//unset direct_link if it exists
$query = sprintf("UPDATE inb_keywords SET background='%s' WHERE id=%d", implode(',',$tab), $id);	//update the string list of backgrounds
$request = mysql_query($query);	
if(!($request)) {
	echo -2;
	exit();
}

if(!unlink("../medias/images/low/".$name.'.png')) {
	echo -3;
	exit();
}
if(!unlink("../medias/images/medium/".$name.'.png')) {
	echo -4;
	exit();
}
if(!unlink("../medias/images/high/".$name.'.png')) {
	echo -5;
	exit();
}
if(!unlink("../medias/images/original/".$name.'.png')) {
	echo -6;
	exit();
}

echo 1;
	
?>
