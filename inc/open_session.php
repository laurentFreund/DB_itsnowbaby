<?php session_start();

################################################################################################
/**********************************************************************************************\
|**		Document:					open_session.php                                         **|
|**		Creation:					June 04, 2012                                            **|
|**		Last modification:			June 27, 2012                                            **|
|**		Authors:					Abdoul aziz SENE                                         **|
|**		Project:					It's Now Baby, Industrial project 7, EMSE & LFKs		 **|
|**		Company:					EMSE, Cycle ISMIN, Promo 2013                            **|
|**		Description:				Starts a session                                         **|
\**********************************************************************************************/
################################################################################################

if(!isset($_GET['login']) || !isset($_GET['pass']) ) {echo -1; exit();} 
extract($_GET);

if(isset($_GET['out'])){
	session_destroy();
	echo 2;
}
else{
	if($login == "lfks" && $pass == "lfks" ){
		$_SESSION['admin'] = 1;
		echo 2;
	}
	else echo -2;
}
?>
