<?php	


################################################################################################
/**********************************************************************************************\
|**		Document:					options.php                                              **|
|**		Creation:					February 28, 2012                                        **|
|**		Last modification:			June 27, 2012                                            **|
|**		Authors:					Abdoul aziz SENE                                         **|
|**		Project:					It's Now Baby, Industrial project 7, EMSE & LFKs		 **|
|**		Company:					EMSE, Cycle ISMIN, Promo 2013                            **|
|**		Description:				returns a list of select options according to variables  **|
\**********************************************************************************************/
################################################################################################


 
// CONNECT to DB
include("connexionbdd.php");
Connexion_BDD();

//if $_GET['check_exist'] doesn't exists, it means we need all values of one field
if(!isset($_GET['check_exist'])){
	$order = 'keyword';
	$field = 'keyword';
	$table = 'inb_keywords';
	$category = "WHERE category =".$_GET['cat'] ;
	$except = "";
	if($_GET['cat'] == -1){
		$order = 'title';
		$field = 'id';
		$table = 'inb_medias_1';
		$category = '';
		$except = "WHERE id <>".$_GET['except'] ;
	}
	//récupération des mots
	$query = sprintf("SELECT %s FROM %s %s %s ORDER BY %s",$field,$table,$category,$except,$order);
	$request = mysql_query($query);
	$res = "";
	$s = "";
	for($i = 0 ; $i < mysql_num_rows($request) ; $i++){
		$res.= $s.mysql_result($request , $i);
		$s = ",";
	}
	echo $res;
}
else{//we chect if this one exists
	$query = sprintf("SELECT * FROM inb_keywords WHERE keyword='%s'",$_GET['check_exist']);
	$request = mysql_query($query);
	if(mysql_num_rows($request)>0) echo 1;
	else echo 0;
}
?>
 