<?php

################################################################################################
/**********************************************************************************************\
|**		Document:					index.php                                                **|
|**		Creation:					February 20, 2012                                        **|
|**		Last modification:			June 27, 2012                                            **|
|**		Authors:					Abdoul aziz SENE                                         **|
|**		Project:					It's Now Baby, Industrial project 7, EMSE & LFKs		 **|
|**		Company:					EMSE, Cycle ISMIN, Promo 2013                            **|
|**		Description:				Home, medias list page                                   **|
\**********************************************************************************************/
################################################################################################

	$current_menu = 1;//to display 'HOME' menu as selected
	$title = utf8_encode("Medias list");//title for the window
	include("header.php");//load the header

//some variables for DB success in process		
$error = false;
$errorText = "";
	
//load variables	
include("inc/config.php");

//connect to DB
include("inc/connexionbdd.php");
Connexion_BDD();

function display_page_selection($f1, $f2){
	?>
	<div class="wrapper" >
		<div class="grid_1" onclick="next_page($('#search_input').val(),<?php echo $f2; ?>,-1);" style='margin:0px;text-align:center;padding:5px;background:url("images/input_color4.png"); font-size:1.3em; border-radius:5px;cursor:pointer;font-weight:bold;' > <center>&lt;&lt;</center> </div>
		<div class="grid_2" style='padding:0px;width:100px;margin: 0 0px;' ><select style='width:100px;margin:0px;' id="select_page_<?php echo $f1; ?>" onchange="field_search_for($('#search_input').val(),<?php echo $f2; ?>,this.value);" ></select> </div>
		<div class="grid_1" onclick="next_page($('#search_input').val(),<?php echo $f2; ?>,1);" style='margin:0px;text-align:center;padding:5px;background:url("images/input_color4.png"); font-size:1.3em; border-radius:5px;cursor:pointer;font-weight:bold;' > <center>&gt;&gt;</center> </div>
	</div>
	<?php
}

//the function to display the list of one type of media
function display_medias_list($_field){
	global $error,$errorText,$public;//the variables for DB success in process
	//and somoe other variables changing occording to the media type
	$player_or_reader = ''; $link_type=0; $media_type=0; $field = ''; $details = ''; $images_path = '';$images_path_original = '';
	switch($_field){
		case 1/*archive*/: $field = 'archive'; $link_type=LINK_TYPE_RELATIVE; $media_type=MEDIATYPE_TEXT; break;
		case 2/*image*/: $field = 'image'; $link_type=LINK_TYPE_RELATIVE; $media_type=MEDIATYPE_IMAGE; break;
		case 3/*audio*/: $field = 'audio'; $link_type=LINK_TYPE_RELATIVE; $media_type=MEDIATYPE_AUDIO; break;
		case 4/*video*/: $field = 'video'; $link_type=LINK_TYPE_RELATIVE; $media_type=MEDIATYPE_VIDEO; break;
		default: $field = 'text'; $link_type=LINK_TYPE_NO_LINK; $media_type=MEDIATYPE_TEXT; break;
	}
	
	//we get the list of medias
	$query = sprintf("SELECT * FROM inb_medias_1 WHERE mediatype=%d AND link_type=%d ORDER BY title",$media_type,$link_type);
	$request = mysql_query($query);
	if(!($request))
	{
		$errorText .= "An error has occurred during the information gathering (level 0) from the database: <br />".mysql_error()."<br /> ";
		$error = true;
	}
	$message = '';//a message if the list is empty
	if(mysql_num_rows($request) <= 0) 
		$message = "
		<div class='wrapper' >
			<div class='grid_3' >&nbsp;</div>
			<div class='grid_6' >
				<div class='info-box' style='width:350px;border-radius:10px;' >
					<p class='icon'><img src='images/icon-info.png' ></p>
					No ".$field." registered yet.
				</div>
			</div>
		</div>";
		
	
	if($_field == 0/*text*/) {//texts are different from other
	?>
		<div id='text' >
			<?php display_page_selection($field, $_field); ?>
			<div class="wrapper" style='margin-left:0px;' >
				<?php if(!$public){ ?>
				<div class="grid_8" style="width:640px;background: url('images/input_color4.png'); margin:1px; font-size:1.3em; border-radius:5px;cursor:pointer;" >
					<div class="grid_6" style="border-radius:15px;width:510px;padding:5px;font-weight:bold;"  > Title </div>							
					<div class="grid_2" style="text-align:center;width:70px;padding:5px;font-weight:bold;" > Action </div>
				</div>
				<div class="grid_1" style='text-align:center;padding:5px;background:url("images/input_color4.png"); font-size:1.3em; border-radius:5px;cursor:pointer;font-weight:bold;' > Id </div>
				<div class="grid_1" style='text-align:center;padding:5px;background:url("images/input_color4.png"); font-size:1.1em; border-radius:5px;cursor:pointer;font-weight:bold;' > Previous </div>
				<div class="grid_1" style='text-align:center;padding:5px;background:url("images/input_color4.png"); font-size:1.3em; border-radius:5px;cursor:pointer;font-weight:bold;' > Next </div>
				<?php } else { ?>
					<div class="grid_11" style='margin-left:1px;padding:5px;background:url("images/input_color4.png"); font-size:1.3em; border-radius:5px;cursor:pointer;font-weight:bold;' > Titre </div>
				<?php } ?>
			</div>
			<div id='text_in' ></div>
		</div>
	<?php
	}
	else {// else this is a media with an uploaded file
		?>
		<div id='<?php echo $field; ?>' >
			<?php display_page_selection($field, $_field); ?>
			<div class="wrapper" >
				<?php if(!$public){ ?>
				<div class="grid_9" style="width:730px;background: url(images/input_color4.png); margin:1px; font-size:1.3em; height:30px; border-radius:5px;cursor:pointer;" >
					<div class="grid_6" style="border-radius:15px;width:540px;padding:5px;font-weight:bold;" > Title </div>
					<div class="grid_2" style="text-align:right;width:110px;padding:5px;font-weight:bold;" > Actions </div>
				</div>
				<div class="grid_1" style='text-align:center;padding:5px;background: url(images/input_color4.png);border-radius:5px;cursor:pointer;font-weight:bold;' > Id </div>
				<div class="grid_1" style='text-align:center;padding:5px;background: url(images/input_color4.png);border-radius:5px;cursor:pointer;font-weight:bold;' > Type </div>
				<?php } else { ?>
					<div class="grid_10" style='margin-left:1px;padding:5px;background:url("images/input_color4.png"); font-size:1.3em; border-radius:5px;cursor:pointer;font-weight:bold;' > Titre </div>
					<div class="grid_1" style='text-align:center;padding:5px;background:url("images/input_color4.png"); font-size:1.3em; border-radius:5px;cursor:pointer;font-weight:bold;' > Type </div>
				<?php } ?>
			</div>
			<?php 
			if($_field == 3) { ?>
			<script type="text/javascript" charset="utf8" >
				var sub_type = [];
			</script>
			<?php } ?>
			<div id='<?php echo $field.'_in'; ?>' ></div>
		</div>
		<?php
	}
}

?>

	
	
<script type="text/javascript" charset="utf8" >
	
	var tab_of_areas = [];
	var one_area = [];
	var text_visible = "";
	var tag = "";
	var close_tag = "</span>";
	var shift_tab = new Array();
	var shift = 0;
	var count = 0;
	
	
	//if there is colored key area before this one, we add the tag lenght to the shift
	function shift_fct(x,tab)	{
		var res = 0;
		for(var i in tab)
		{
			if(tab[i][0] < x) res += tab[i][1];
		}
		return res;
	}
	
	
	var warning = false;
	function close_warning(){
		if(warning){
			$("#warning").dialog( 'close' );
			warning = false;
		}
	}
	
	//delete the media using delete_media.php to realize operations on the db 
	function delete_media(id, div, file, media) {
	var first_click = true;//to fordid the user to say yes several time, we valid only the first time, the first clic
	//we oppen a prompt window using jquery ui dialog method
	$('#warning').css("display", 'inline-block');//activate the bloc
	$('#warning').html("Do you really to delete this "+media+" ? (id "+id+")");//update the content
	$( "#dialog:ui-dialog" ).dialog( "destroy" );//destroy other ui-dialog windows
	$( "#warning" ).dialog({resizable: false,width: 210,modal: true,show: 'slide',hide: 'clip',	buttons: {
			"No": function() {
				$( this ).dialog( 'close' );
			},
			"Yes": function() {//if the user say yes, we can delete 
				if(first_click){
					var filee = ''; if(file != '') filee = '&thefile='+file;//if there is a file to delete
					var action = ajax_open("inc/delete_media.php?id="+id+filee);//we open php file which can delete the media from the database, action will contain the answer
					
					$('#'+div).slideUp('fast');
					$( "#warning" ).dialog({resizable: false,width: 210,modal: true,show: 'slide',hide: 'clip',	buttons: {// set ui-dialog prefenrences
						"Ok": function() {
							$( this ).dialog( 'close' );
							warning = false;
						}
					}});
					$('#warning').html("The "+media+" has been successfully deleted.");
					warning =true;
					setTimeout("close_warning()",1000);//close the notification window after 1 sec 
					
					first_click = false;
				}
			}
		}
	});
	$( "#warning" ).dialog('open');//open the ui-dialog window
	}
	
	// for the subtitles, some wrong entries are forbidden:end must be higher then begin, and both must be numbers
	
	
	//show a line where a subtitle can be added
	function add_subtitle(id, subtitles_nb){
		//create a new (hidden) line
		$('#subtitles_'+id).append("<div class='wrapper' id='"+id+"_wrapper_"+subtitles_nb+"' style='display:none;' ><div class='grid_1' style='background:url(images/input.png) 0 0 repeat;text-align:center;font-size:1.3em;height:20px;margin:1px;padding-top:6px;border-radius:5px;' > "+subtitles_nb+" </div> <div class='grid_1'><input disabled type='text' id='"+id+"_begin_"+subtitles_nb+"' value='"+$('#'+id+'_end_'+(subtitles_nb-1)).val()+"' style='width:50px;' /></div><div class='grid_1'> <input type='text' id='"+id+"_end_"+subtitles_nb+"' style='width:50px;' /></div><div class='grid_6' > <input type='text' id='"+id+"_subtitle_"+subtitles_nb+"' style='width:460px;' /></div></div>");
		sub_type[id][subtitles_nb] = 0 ;
		//show the new line with jquery slide method
		$('#'+id+'_wrapper_'+subtitles_nb).slideDown(function(){
			$('#'+id+'_add_'+(subtitles_nb-1)).fadeOut();//we hide the previous add button
			if(subtitles_nb>2) $('#'+id+'_del_'+(subtitles_nb-1)).fadeOut();//we hide the previous remove button
			$('#'+id+'_end_'+(subtitles_nb-1)).keyup(function(){//anytime the previous end is edited, this begin copy the value: the end of one line is the begin of the next
				$('#'+id+'_begin_'+subtitles_nb).val($(this).val());
			});
		});
	}
	
	
	
	
	//if there are already some registered subtitles, we load them
	function load_subtitles(id,subtitles){
		if(subtitles != ''){
			//in the DB, all subtitles are on one string, separated by |
			var lines = explode('|',subtitles);//so put them in an array
			
			//we add first line
			var sub1 = explode('~',lines[0]);
			sub_type[id][1] = sub1[2];
			$('#'+id+'_end_1').val(parseInt(sub1[1]));
			if(sub1[2] == '0') $('#'+id+'_subtitle_1').val(sub1[3]);
			else if(sub1[2] == '1') $('#'+id+'_subtitle_1').val("The text number "+sub1[3]+" : "+ajax_open("inc/title.php?id="+sub1[3]));
				
			
			
			//we add other lines
			for(var i = 1 ; i < lines.length ; i++){
				add_subtitle(id, (i+1));
				var sub = explode('~',lines[i]);
				sub_type[id][i+1] = sub[2];
				$('#'+id+'_begin_'+(i+1)).val(parseInt(sub[0]));
				$('#'+id+'_end_'+(i+1)).val(parseInt(sub[1]));
				if(sub[2] == '0') $('#'+id+'_subtitle_'+(i+1)).val(sub[3]);
				else if(sub[2] == '1'){
					$('#'+id+'_subtitle_'+(i+1)).val("The text number "+sub[3]+" : "+ajax_open("inc/title.php?id="+sub[3]));
				}
			}
			
			//we hide buttons
			for(var i = 2 ; i < lines.length ; i++){
				$('#'+id+'_add_'+i).css("display","none");
				$('#'+id+'_del_'+i).css("display","none");
			}
			$('#'+id+'_add_'+lines.length).fadeIn();
			if(lines.length > 1)
				$('#'+id+'_add_1').css("display","none");
			
			//return current lines number
			return lines.length ;
		}
		else return 1;
	}
	
	
	//calculate the number pages for all fields, and generate the select options
	function pagination(val,field){
		var _field = 0,
			mediatype = 0,
			link_type = 0,
			nb = 0,
			nb_page = 0;
		
		switch (field) {
			case 1 : _field = 'archive' ; mediatype = <?php echo MEDIATYPE_TEXT ; ?> ; link_type = <?php echo LINK_TYPE_RELATIVE; ?> ; break;
			case 2 : _field = 'image' 	; mediatype = <?php echo MEDIATYPE_IMAGE; ?> ; link_type = <?php echo LINK_TYPE_RELATIVE; ?> ; break;
			case 3 : _field = 'audio' 	; mediatype = <?php echo MEDIATYPE_AUDIO; ?> ; link_type = <?php echo LINK_TYPE_RELATIVE; ?> ; break;
			case 4 : _field = 'video' 	; mediatype = <?php echo MEDIATYPE_VIDEO; ?> ; link_type = <?php echo LINK_TYPE_RELATIVE; ?> ; break;
			default: _field = 'text' ; mediatype = <?php echo MEDIATYPE_TEXT ; ?> ; link_type = <?php echo LINK_TYPE_NO_LINK; ?>  ; break;
		}
		
		
		nb = ajax_open("inc/medias_count.php?mediatype="+mediatype+"&linktype="+link_type+"&val="+val);
		nb_pages = Math.floor(nb/30) + 1 ;
		
		
		
		$('#select_page_'+_field).html("");
		
		
		for(var i = 1 ; i <= nb_pages ; i++){
			$('#select_page_'+_field).append("<option value='"+i+"' >page "+i+"</option>");
		}
	}
	
	//go to next or previous page
	function next_page(val,field,sens){
		var _field = 0,
			mediatype = 0,
			link_type = 0,
			nb = 0,
			nb_page = 0;
		
		switch (field) {
			case 1 : _field = 'archive' ; mediatype = <?php echo MEDIATYPE_TEXT ; ?> ; link_type = <?php echo LINK_TYPE_RELATIVE; ?> ; break;
			case 2 : _field = 'image' 	; mediatype = <?php echo MEDIATYPE_IMAGE; ?> ; link_type = <?php echo LINK_TYPE_RELATIVE; ?> ; break;
			case 3 : _field = 'audio' 	; mediatype = <?php echo MEDIATYPE_AUDIO; ?> ; link_type = <?php echo LINK_TYPE_RELATIVE; ?> ; break;
			case 4 : _field = 'video' 	; mediatype = <?php echo MEDIATYPE_VIDEO; ?> ; link_type = <?php echo LINK_TYPE_RELATIVE; ?> ; break;
			default: _field = 'text' ; mediatype = <?php echo MEDIATYPE_TEXT ; ?> ; link_type = <?php echo LINK_TYPE_NO_LINK; ?>  ; break;
		}
		
		
		nb = ajax_open("inc/medias_count.php?mediatype="+mediatype+"&linktype="+link_type+"&val="+val);
		nb_pages = Math.floor(nb/30) + 1 ;
		
		
		if($('#select_page_'+_field).val() < nb_pages && sens > 0){
			document.getElementById('select_page_'+_field).selectedIndex=$('#select_page_'+_field).val() ;
			field_search_for($('#search_input').val(), field, $('#select_page_'+_field).val());
		}
		else if($('#select_page_'+_field).val() > 1 && sens < 0){
			document.getElementById('select_page_'+_field).selectedIndex=$('#select_page_'+_field).val()-2 ;
			field_search_for($('#search_input').val(), field, $('#select_page_'+_field).val());
		}
	}
	
	
	//searching for a string using the search.php file
	function search_for(val){
		$('#s_loader').fadeIn('fast',function(){
			$('#archive_in').html(ajax_open("inc/search.php?mediatype=<?php echo MEDIATYPE_TEXT; ?>&linktype=<?php echo LINK_TYPE_RELATIVE; ?>&_field=1&public=<?php echo $public; ?>&val="+val+"&page=1"));
			$('#nb_archive').html(ajax_open("inc/medias_count.php?mediatype=<?php echo MEDIATYPE_TEXT; ?>&linktype=<?php echo LINK_TYPE_RELATIVE; ?>&val="+val));
			pagination(val,1);
			
			$('#image_in').html(ajax_open("inc/search.php?mediatype=<?php echo MEDIATYPE_IMAGE; ?>&linktype=<?php echo LINK_TYPE_RELATIVE; ?>&_field=2&public=<?php echo $public; ?>&val="+val+"&page=1"));
			$('#nb_image').html(ajax_open("inc/medias_count.php?mediatype=<?php echo MEDIATYPE_IMAGE; ?>&linktype=<?php echo LINK_TYPE_RELATIVE; ?>&val="+val));
			pagination(val,2);
			
			$('#audio_in').html(ajax_open("inc/search.php?mediatype=<?php echo MEDIATYPE_AUDIO; ?>&linktype=<?php echo LINK_TYPE_RELATIVE; ?>&_field=3&public=<?php echo $public; ?>&val="+val+"&page=1"));
			$('#nb_audio').html(ajax_open("inc/medias_count.php?mediatype=<?php echo MEDIATYPE_AUDIO; ?>&linktype=<?php echo LINK_TYPE_RELATIVE; ?>&val="+val));
			pagination(val,3);
			
			$('#video_in').html(ajax_open("inc/search.php?mediatype=<?php echo MEDIATYPE_VIDEO; ?>&linktype=<?php echo LINK_TYPE_RELATIVE; ?>&_field=4&public=<?php echo $public; ?>&val="+val+"&page=1"));
			$('#nb_video').html(ajax_open("inc/medias_count.php?mediatype=<?php echo MEDIATYPE_VIDEO; ?>&linktype=<?php echo LINK_TYPE_RELATIVE; ?>&val="+val));
			pagination(val,4);
			
			$('#nb_text').html(ajax_open("inc/medias_count.php?mediatype=<?php echo MEDIATYPE_TEXT; ?>&linktype=<?php echo LINK_TYPE_NO_LINK; ?>&val="+val));
			pagination(val,0);
			$('#text_in').html(ajax_open("inc/search.php?mediatype=<?php echo MEDIATYPE_TEXT; ?>&linktype=<?php echo LINK_TYPE_NO_LINK; ?>&_field=0&public=<?php echo $public; ?>&val="+val+"&page=1"));
			
			
			setTimeout(function(){$('#s_loader').fadeOut('fast')},100);
			$('.normaltip').aToolTip();
		});
	} 
	
	//searching for a string in one field using the search.php file
	function field_search_for(val,field, page){
		var _field = 0,
			mediatype = 0,
			link_type = 0;
		
		switch (field) {
			case 1 : _field = 'archive' ; mediatype = <?php echo MEDIATYPE_TEXT ; ?> ; link_type = <?php echo LINK_TYPE_RELATIVE; ?> ; break;
			case 2 : _field = 'image' 	; mediatype = <?php echo MEDIATYPE_IMAGE; ?> ; link_type = <?php echo LINK_TYPE_RELATIVE; ?> ; break;
			case 3 : _field = 'audio' 	; mediatype = <?php echo MEDIATYPE_AUDIO; ?> ; link_type = <?php echo LINK_TYPE_RELATIVE; ?> ; break;
			case 4 : _field = 'video' 	; mediatype = <?php echo MEDIATYPE_VIDEO; ?> ; link_type = <?php echo LINK_TYPE_RELATIVE; ?> ; break;
			default: _field = 'text' ; mediatype = <?php echo MEDIATYPE_TEXT ; ?> ; link_type = <?php echo LINK_TYPE_NO_LINK; ?>  ; break;
		}
	
		$('#s_loader').fadeIn('fast',function(){
			$('#nb_'+_field).html(ajax_open("inc/medias_count.php?mediatype="+mediatype+"&linktype="+link_type+"&val="+val));
			$('#'+_field+'_in').html(ajax_open("inc/search.php?mediatype="+mediatype+"&linktype="+link_type+"&_field=0&public=<?php echo $public; ?>&val="+val+"&page="+page));
			setTimeout(function(){$('#s_loader').fadeOut('fast')},100);
			$('.normaltip').aToolTip();
		});
	} 
	
	

	
	
	
	//hide and display a media includes the load and the free of the media:
	// the medias players aren't loaded at start (memory reasons)
	function hide_media(field,id){
		$('#'+field+'_'+id).slideUp('slow', function(){
			$('#'+field+'_'+id).html("");
		});
	}
	
	//load jwplayer for audio and video player
	function show_media(field,id,player){
		$('#'+field+'_'+id).html("<div class='grid_1' >&nbsp;</div><div class='grid_9' style='text-align:justify;' >"+player+"</div>");
		if(field == 'video') jwplayer("video"+id).setup({flashplayer: "jwplayer/player.swf"});	
		if(field == 'audio') jwplayer("audio"+id).setup({flashplayer: "jwplayer/player.swf"});	
		$('#'+field+'_'+id).slideDown('slow');
	}
		
	
	
</script>


<?php if(!$public){ ?>	
	<br>
		<center><h1>Medias list</h1></center>
	<br>
	<div style='width:380px;height:40px;position:relative;left:580px;' >
		<div style='width:40px;height:40px;float:left;margin:0px;' ><img src='images/search_loader.gif' id='s_loader' style='display:none;border-radius:15px; ' /></div>
		<input type='text' id='search_input' onkeyup="search_for(this.value);" style="display:inline;border:white 2px outset;border-radius:15px; background: url(images/bt_search.png) right center no-repeat;margin:5px;" />
	</div>

<?php } ?>	
	
	<div id='list' >
		<ul>
			<li><a href='#text' > Texts ( <span id='nb_text' ></span> ) </a></li>
			<li><a href='#archive' > Archives ( <span id='nb_archive' ></span> ) </a></li>
			<li><a href='#image' > Images ( <span id='nb_image' ></span> ) </a></li>
			<li><a href='#audio' > Audios ( <span id='nb_audio' ></span> ) </a></li>
			<li><a href='#video' > Videos ( <span id='nb_video' ></span> ) </a></li>
		</ul>
		
		<?php
		display_medias_list(0);//texts
		display_medias_list(1);//archives
		display_medias_list(2);//images
		display_medias_list(3);//audios tracks
		display_medias_list(4);//videos
		?>
	
	</div>
	
	<div id="warning" title="Warning" ></div>
	
	<script type="text/javascript" src='js/jquery.atooltip.pack.js' charset="utf8" > </script>
	<script type="text/javascript" charset="utf8" > 
		$('#list').tabs(); // jquery ui tabs method
		
		$('#s_loader').fadeIn('fast',function(){
			$('#archive_in').html(ajax_open("inc/search.php?mediatype=<?php echo MEDIATYPE_TEXT; ?>&linktype=<?php echo LINK_TYPE_RELATIVE; ?>&_field=1&public=<?php echo $public ? 1 : 0 ; ?>&val=&page=1"));
				$('#nb_archive').html(ajax_open("inc/medias_count.php?mediatype=<?php echo MEDIATYPE_TEXT; ?>&linktype=<?php echo LINK_TYPE_RELATIVE; ?>&val="));
			$('#image_in').html(ajax_open("inc/search.php?mediatype=<?php echo MEDIATYPE_IMAGE; ?>&linktype=<?php echo LINK_TYPE_RELATIVE; ?>&_field=2&public=<?php echo $public ? 1 : 0 ; ?>&val=&page=1"));
				$('#nb_image').html(ajax_open("inc/medias_count.php?mediatype=<?php echo MEDIATYPE_IMAGE; ?>&linktype=<?php echo LINK_TYPE_RELATIVE; ?>&val="));
			$('#audio_in').html(ajax_open("inc/search.php?mediatype=<?php echo MEDIATYPE_AUDIO; ?>&linktype=<?php echo LINK_TYPE_RELATIVE; ?>&_field=3&public=<?php echo $public ? 1 : 0 ; ?>&val=&page=1"));
				$('#nb_audio').html(ajax_open("inc/medias_count.php?mediatype=<?php echo MEDIATYPE_AUDIO; ?>&linktype=<?php echo LINK_TYPE_RELATIVE; ?>&val="));
			$('#video_in').html(ajax_open("inc/search.php?mediatype=<?php echo MEDIATYPE_VIDEO; ?>&linktype=<?php echo LINK_TYPE_RELATIVE; ?>&_field=4&public=<?php echo $public ? 1 : 0 ; ?>&val=&page=1"));
				$('#nb_video').html(ajax_open("inc/medias_count.php?mediatype=<?php echo MEDIATYPE_VIDEO; ?>&linktype=<?php echo LINK_TYPE_RELATIVE; ?>&val="));
				$('#nb_text').html(ajax_open("inc/medias_count.php?mediatype=<?php echo MEDIATYPE_TEXT; ?>&linktype=<?php echo LINK_TYPE_NO_LINK; ?>&val="));
			$('#text_in').html(ajax_open("inc/search.php?mediatype=<?php echo MEDIATYPE_TEXT; ?>&linktype=<?php echo LINK_TYPE_NO_LINK; ?>&_field=0&public=<?php echo $public ? 1 : 0 ; ?>&val=&page=1"));
			
			for(var i = 0 ; i<5 ; i++)
				pagination('',i);
			
			setTimeout(function(){$('#s_loader').fadeOut('fast')},100);
			$('.normaltip').aToolTip();
		});
	</script>
<?php	
	include("footer.php");
?>
