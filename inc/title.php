<?php

################################################################################################
/**********************************************************************************************\
|**		Document:					title.php                                                **|
|**		Creation:					May 21, 2012                                             **|
|**		Last modification:			June 27, 2012                                            **|
|**		Authors:					Abdoul aziz SENE                                         **|
|**		Project:					It's Now Baby, Industrial project 7, EMSE & LFKs		 **|
|**		Company:					EMSE, Cycle ISMIN, Promo 2013                            **|
|**		Description:				returns a title of a media                               **|
\**********************************************************************************************/
################################################################################################

//connect to DB
include("connexionbdd.php");
Connexion_BDD();

//load config file
include("config.php");

//exit if variables aren't set
if(!isset($_GET['id'])) {echo -1; exit();} 
$id = $_GET['id'];

//get the media metadata
$query0 = sprintf("SELECT title FROM inb_medias_1 WHERE id=%d", $id);
$request0 = mysql_query($query0);
echo mysql_result($request0,0);
 
?>
