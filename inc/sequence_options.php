<?php

################################################################################################
/**********************************************************************************************\
|**		Document:					sequence_options.php                                     **|
|**		Creation:					April 16, 2012                                           **|
|**		Last modification:			June 27, 2012                                            **|
|**		Authors:					Abdoul aziz SENE                                         **|
|**		Project:					It's Now Baby, Industrial project 7, EMSE & LFKs		 **|
|**		Company:					EMSE, Cycle ISMIN, Promo 2013                            **|
|**		Description:				returns a list of select options                         **|
\**********************************************************************************************/
################################################################################################


//loading the configuration file
include("config.php"); 

//connect to DB
include("connexionbdd.php");
Connexion_BDD();

//the text, the previous and the next must be different, so when one is set it become an exception for the rest
//then list return all other value in an 'option' tag
if(!isset($_GET['except1']) || !isset($_GET['except2']) || !isset($_GET['selected']) || !isset($_GET['name'])) die();
extract($_GET);
	
$query_except = "";
if($except1 != '' && $except2 == '') $query_except = "AND id<>".$except1;
else if($except2 != '' && $except1 == '') $query_except = "AND id<>".$except2;
if($except1 != '' && $except2 != '') $query_except = "AND id NOT IN (".$except1.",".$except2.")";

$query = sprintf("select * from inb_medias_1 WHERE mediatype='%d' AND link_type='%d' %s ORDER by title",MEDIATYPE_TEXT ,LINK_TYPE_NO_LINK, $query_except );
$request = mysql_query($query);

$options = '<option value=0 >  ---- '.$name.' text ----  </option>';
$s = '';

if($request){
	while($tab = mysql_fetch_assoc($request)){
		if($selected != 0 && $tab[id] == $selected) $s = 'selected';
		$options .= "<option ".$s." value=".$tab['id']." >".$tab['title']."</option>";
		$s = '';
	}
}
		
echo "<div class='grid_4 aligncenter' style='background: url(images/input_color4.png);border-radius:5px;height:70px;margin-bottom:15px;' >
		<span style='display:inline-block; line-height:1.2em; font-size:15px; color:#fff; text-transform:uppercase; padding:5px 20px 8px 15px; letter-spacing:1px; border-radius:2px;' > ".$name." text </span><br>
		<select id='".$name."' name='".$name."' >".$options."</select>
	</div>" ;


?>