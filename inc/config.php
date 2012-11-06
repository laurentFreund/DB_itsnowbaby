<?php

################################################################################################
/**********************************************************************************************\
|**		Document:					config.php                                               **|
|**		Creation:					March 18, 2012                                           **|
|**		Last modification:			June 27, 2012                                            **|
|**		Authors:					Abdoul aziz SENE                                         **|
|**		Project:					It's Now Baby, Industrial project 7, EMSE & LFKs		 **|
|**		Company:					EMSE, Cycle ISMIN, Promo 2013                            **|
|**		Description:				some constants                                           **|
\**********************************************************************************************/
################################################################################################


/*******************************************************************************************\
|**   Images                                                                              **|
\*******************************************************************************************/

//define contants for image resizing
//low resolution image
if(!defined("LOW_RESOLUTION_IMG_WIDTH")) 		define("LOW_RESOLUTION_IMG_WIDTH", 960); 
if(!defined("LOW_RESOLUTION_IMG_HEIGHT")) 		define("LOW_RESOLUTION_IMG_HEIGHT", 640); 

//medium resolution image
if(!defined("MEDIUM_RESOLUTION_IMG_WIDTH")) 	define("MEDIUM_RESOLUTION_IMG_WIDTH", 1280); 
if(!defined("MEDIUM_RESOLUTION_IMG_HEIGHT")) 	define("MEDIUM_RESOLUTION_IMG_HEIGHT", 800); 

//low resolution image
if(!defined("HIGH_RESOLUTION_IMG_WIDTH")) 		define("HIGH_RESOLUTION_IMG_WIDTH", 1920); 
if(!defined("HIGH_RESOLUTION_IMG_HEIGHT")) 		define("HIGH_RESOLUTION_IMG_HEIGHT", 1080);



/*******************************************************************************************\
|**   Keywords                                                                            **|
\*******************************************************************************************/

if(!defined("KEYWORD_THEME_CAT")) 				define("KEYWORD_THEME_CAT", 0);
if(!defined("KEYWORD_LOCATION_CAT")) 			define("KEYWORD_LOCATION_CAT", 1);
if(!defined("KEYWORD_PERSON_CAT")) 				define("KEYWORD_PERSON_CAT", 2);
if(!defined("KEYWORD_GENRE_CAT")) 				define("KEYWORD_GENRE_CAT", 3);
if(!defined("KEYWORD_MISC_CAT")) 				define("KEYWORD_MISC_CAT", 4);
if(!defined("KEYWORD_SYSTEM_CAT")) 				define("KEYWORD_SYSTEM_CAT", 5);



/*******************************************************************************************\
|**   Media types                                                                         **|
\*******************************************************************************************/

if(!defined("MEDIATYPE_TEXT")) 					define("MEDIATYPE_TEXT", 0);
if(!defined("MEDIATYPE_AUDIO")) 				define("MEDIATYPE_AUDIO", 1);
if(!defined("MEDIATYPE_IMAGE")) 				define("MEDIATYPE_IMAGE", 2);
if(!defined("MEDIATYPE_VIDEO")) 				define("MEDIATYPE_VIDEO", 3);



/*******************************************************************************************\
|**   Link types                                                                          **|
\*******************************************************************************************/

if(!defined("LINK_TYPE_NO_LINK")) 				define("LINK_TYPE_NO_LINK", 0);
if(!defined("LINK_TYPE_RELATIVE")) 				define("LINK_TYPE_RELATIVE", 1);
if(!defined("LINK_TYPE_HTTP")) 					define("LINK_TYPE_HTTP", 2);
if(!defined("LINK_TYPE_EMBED")) 				define("LINK_TYPE_EMBED", 3);



/*******************************************************************************************\
|**   Medias list display dimensions                                                      **|
\*******************************************************************************************/

if(!defined("MEDIAS_LIST_DISPLAY_WIDTH")) 		define("MEDIAS_LIST_DISPLAY_WIDTH", 700);
if(!defined("MEDIAS_LIST_DISPLAY_HEIGHT")) 		define("MEDIAS_LIST_DISPLAY_HEIGHT", 400);


/*******************************************************************************************\
|**   Header menu                                                                         **|
\*******************************************************************************************/

if(!defined("CURRENT_MENU_HOME")) 				define("CURRENT_MENU_HOME", 1);
if(!defined("CURRENT_MENU_KEYWORDS")) 			define("CURRENT_MENU_KEYWORDS", 2);
if(!defined("CURRENT_MENU_SETTINGS")) 			define("CURRENT_MENU_SETTINGS", 3);






