<?php

################################################################################################
/**********************************************************************************************\
|**		Document:					connexionbdd.php                                         **|
|**		Creation:					February 20, 2012                                        **|
|**		Last modification:			June 27, 2012                                            **|
|**		Authors:					Abdoul aziz SENE                                         **|
|**		Project:					It's Now Baby, Industrial project 7, EMSE & LFKs		 **|
|**		Company:					EMSE, Cycle ISMIN, Promo 2013                            **|
|**		Description:				connexion to the database                                **|
\**********************************************************************************************/
################################################################################################


	//Cette fonction a pour but de se connecter sur la table des entreprises
	function Connexion_BDD()
	{
		$connexion=mysql_connect("localhost", "root", "root"); //Connexion to the database
		if(!$connexion)
		{
			die('Impossible de se connecter : '.mysql_error());
			}
		if(!mysql_select_db('its_now_baby', $connexion)) // table select
		{
			die('Impossible de sélectionner la base de donnée');
		}
		return $connexion;
	}
?>