<?php session_start(); 

################################################################################################
/**********************************************************************************************\
|**		Document:					header.php                                               **|
|**		Creation:					February 20, 2012                                        **|
|**		Last modification:			June 27, 2012                                            **|
|**		Authors:					Abdoul aziz SENE                                         **|
|**		Project:					It's Now Baby, Industrial project 7, EMSE & LFKs		 **|
|**		Company:					EMSE, Cycle ISMIN, Promo 2013                            **|
|**		Description:				Header of the website                                    **|
\**********************************************************************************************/
################################################################################################

if(isset($_SESSION['admin'])) $public = false; else  $public = true;

//loading the configuration file
include("inc/config.php"); 
?>
<!DOCTYPE html>
<html lang="fr">
	<head>
		<title>It's Now BABY! - <?php echo $title; ?></title>
		
		<meta charset="utf8" >
		<meta name="description" content="">
		<meta name="keywords" content="">
		<meta name="author" content="Abdoul Aziz SENE">
		
		<link rel="shortcut icon" type="image/x-icon" href="images/favorite.ico" />
		<link rel="stylesheet" href="css/style.css" />
		<link href="css/custom-theme/jquery-ui-1.8.18.custom.css" rel="stylesheet" type="text/css" />
		
		
		<script type="text/javascript" charset="utf8" src="js/jquery-1.7.1.js"></script>
		<script type="text/javascript" charset="utf8" src="js/cufon-yui.js"></script>
		<script type="text/javascript" charset="utf8" src="js/Lato_400.font.js"></script>
		<script type="text/javascript" charset="utf8" src="js/Lato_Black_900.font.js"></script>
		<script type="text/javascript" charset="utf8" src="js/Lato_italic_400.font.js"></script>
		<script type="text/javascript" charset="utf8" src="js/cufon-replace.js"></script>
		<script type="text/javascript" charset="utf8" src="js/script.js"></script>
		<script type="text/javascript" charset="utf8" src="js/jquery-ui-1.8.18.custom.min.js"></script>
		<script type="text/javascript" charset="utf8" src="js/usefull.js"></script>
		<!-- the Javascript file of JW Player -->
		<script type="text/javascript" src="jwplayer/jwplayer.js"></script>
		
		
		<!--[if lt IE 7]>
			<div class='aligncenter'><a href="http://www.microsoft.com/windows/internet-explorer/default.aspx?ocid=ie6_countdown_bannercode"><img src="http://storage.ie6countdown.com/assets/100/images/banners/warning_bar_0000_us.jpg"border="0"></a></div>  
		<![endif]-->
		<!--[if lt IE 9]>
			<script charset="utf8" src="js/html5.js"></script>
			<link rel="stylesheet" href="css/ie.css"> 
		<![endif]-->
		
		
	</head>
	<body>
	<!--==============================header menu =================================-->
		<header>
			<div class="bgheader2">
				<div class="main2">
					<div class="fleft"><h1><a href="index.php"></a></h1></div>
					<nav>
						<ul class="sf-menu">
							<?php if(!($public)){ ?>
							<li <?php if($current_menu == 1) echo "class='current'"; ?> ><a href="index.php"><span>Home</span><em style="text-align:center;" >Medias list</em></a>
							</li>
							<li <?php if($current_menu == 2) echo "class='current'"; ?> ><a ><span>Upload</span><em style="text-align:center;" >edit a media</em></a>
								<ul>
									<li><a href="text_add.php">Text</a></li>
									<li><a href="archive_add.php">Archive</a></li>
									<li><a href="image_add.php">Image</a></li>
									<li><a href="audio_add.php">Audio</a></li>
									<li><a href="video_add.php">Video</a></li>
									<li><a href="srt_add.php">SRT file</a></li>
									<li><a href="txt_add.php">TXT file</a></li>
								</ul>
							</li>
							<li <?php if($current_menu == 3) echo "class='current'"; ?> ><a href="settings.php"><span>Settings</span><em style="text-align:center;" >and preferences</em></a>
							</li>
							<?php } else { ?>
							<div style='width:700px;height:40px;position:relative;top:50px;' >
								<div style='width:40px;height:40px;float:left;margin:5px;' ><img src='images/search_loader.gif' id='s_loader' style='display:none;border-radius:15px;' /></div>
								<div style="float:left;width:640px;height:31px;margin:3px;padding:0px;background:url(images/body.png) 0 0 repeat;border-radius:15px;" >
									<input type='text' id='search_input' onkeyup="search_for(this.value);" style="width:640px;display:inline;border:white 2px outset;border-radius:15px; background: url(images/bt_search.png) right center no-repeat;margin:0px;" />
								</div>
							</div>
							<?php } ?>
						</ul>
						<div class="clear"></div>
					</nav>
				</div>
			</div>
		</header>
	<!--==============================header menu end=================================-->
		
		<div class="contentbg">
			<div class="container_12" >
