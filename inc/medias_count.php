<?php

################################################################################################
/**********************************************************************************************\
|**		Document:					media_count.php                                          **|
|**		Creation:					May 31, 2012                                             **|
|**		Last modification:			June 27, 2012                                            **|
|**		Authors:					Abdoul aziz SENE                                         **|
|**		Project:					It's Now Baby, Industrial project 7, EMSE & LFKs		 **|
|**		Company:					EMSE, Cycle ISMIN, Promo 2013                            **|
|**		Description:				returns the medias nuimber according to GET variables    **|
\**********************************************************************************************/
################################################################################################

//connect to DB
include("connexionbdd.php");
Connexion_BDD();

//exit if variables aren't set
if(!isset($_GET['mediatype']) || !isset($_GET['linktype'])|| !isset($_GET['val'])) {echo -1; exit();} 
extract($_GET);

//get the media metadata
$query0 = "SELECT count(*) FROM inb_medias_1 WHERE mediatype=".$mediatype." AND link_type=".$linktype." AND 
		   (title LIKE '%".$val."%' OR 
			authors LIKE '%".$val."%' OR 
			text LIKE '%".$val."%' OR 
			themes LIKE '%".$val."%' OR 
			locations LIKE '%".$val."%' OR 
			persons LIKE '%".$val."%' OR 
			genre LIKE '%".$val."%' OR 
			misc LIKE '%".$val."%' OR 
			key_areas LIKE '%".$val."%' OR 
			subtitles LIKE '%".$val."%')";
$request0 = mysql_query($query0);
echo mysql_result($request0,0); 

?>
