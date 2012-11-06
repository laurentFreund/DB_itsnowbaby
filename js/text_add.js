
	//Some global variables
	var text = '';
	var text_visible = '';
	var last_check = -1;
	var clic_areas_nb = -1;
	var edit_text = true;
	
	//the tag to color a clic area blue
	var tag_color = "<span style='color:#5555ff;font-size:1.1em;font-weight:bold;' >";
	var close_tag = "</span>";
	
	//arrays to store all clic areas, their positions and thematics 
	var tab_of_areas = [];
	var tab_of_areas_position = [];
	
	//we will use some kind of state machine, here are the different states
	var CLIC_AREA = {"ON_TEXT":1,"ON_NEW_AREA":2,"ON_ADDED_AREA":3,"ON_ERROR":4};
	
	//the actual state
	var state = CLIC_AREA.ON_TEXT;
	


	//after setting all thematics, we must put all information in inputs before submitting the form.
	//This function generate all inputs and put in information
	//the keywords are set from a multiple select field. this function transform selected keywords into strings and put them into to hiddden html inputs
	//so after submit, we get strings back through $_POST variables
	function generate_inputs()	{
		var count = 0;
		var temp = "";
		var s = "";
		
		//lenth
		$('#add_form').append("<input type='hidden' name='text_length' value='"+text.length+"' />");
		//lenth end
		
		//key_areas
		var val = "";// will contain this: begin~end~themes~locations~persons~genre~misc~system~direct_links| 'NEXT KEY AREA'
		for(var i = 0 ; i <= clic_areas_nb ; i++ )
		{
			//begin
			val += tab_of_areas_position[i][0]+"~";
			
			//end
			val += tab_of_areas_position[i][1]+"~";
			
			//keywords
			var field ='';
			for(var k = 0 ; k< 6 ; k++){
				count = 0; temp = "";
				switch(k){
					case 1: field ='location'; break;
					case 2: field ='person'; break;
					case 3: field ='genre'; break;
					case 4: field ='misc'; break;
					case 5: field ='system'; break;
					default: field ='theme'; break;
				}
				for(var j in tab_of_areas[i][k])
				{
					
					if(count >0 ) s = ","; else s = "";
					if(tab_of_areas[i][k][j] > 0)
					{
						if(tab_of_areas[i][k][j] <= max_value){
							temp += s+tab_keywords[k][j];
						}
						else{
							temp += s+options_values[field][tab_of_areas[i][k][j] - max_value];
						}
						count++;
					}
				}
				val += temp+"~";
			}
	
			
			//direct_links
			count = 0; temp = "";
			for(var j in tab_of_areas[i][6])
			{
				if(count >0 ) s = ","; else s = "";
				if(tab_of_areas[i][6][j] > 0)
				{
					temp += s+tab_direct_link[j];
					count++;
				}
			}
			val += temp+"|";
		}
		
		val = val.substr(0,val.length - 1);
		$('#add_form').append("<input type='hidden' name='key_areas' value='"+val+"' />");
		//key areas end
		
		//keywords
		var field =''; var _field = '';
		for(var k = 0 ; k< 6 ; k++){
			count = 0; temp = "";
			switch(k){
				case 1: field ='location'; _field ='locations'; break;
				case 2: field ='person'; _field ='persons'; break;
				case 3: field ='genre'; _field ='genre'; break;
				case 4: field ='misc'; _field ='misc'; break;
				case 5: field ='system'; _field ='system'; break;
				default: field ='theme'; _field = 'themes'; break;
			}
			for(var i in tab_of_keywords[k])
			{
				if(count >0 ) s = ","; else s = "";
				if(tab_of_keywords[k][i] > 0)
				{
					if(tab_of_keywords[k][i] <= max_value){
						temp += s+tab_keywords[k][i];
					}
					else{
						temp += s+options_values[field][tab_of_keywords[k][i] - max_value];
					}
					count++;
				}
			}
			$('#add_form').append("<input type='hidden' name='"+_field+"' value='"+temp+"' />");
		}
		//keywords end
		
		//direct_links
		count = 0; temp = "";
		for(var i in tab_of_keywords[6])
		{
			if(count >0 ) s = ","; else s = "";
			if(tab_of_keywords[6][i] > 0)
			{
				temp += s+tab_direct_link[i];
				count++;
			}
		}
		$('#add_form').append("<input type='hidden' name='direct_links' value='"+temp+"' />");
		//direct_links end
		
		//keywords added
		var field =''; var _field = '';
		for(var k = 0 ; k< 6 ; k++){
			count = 0; temp = "";
			switch(k){
				case 1: field ='location'; _field ='locations'; break;
				case 2: field ='person'; _field ='persons'; break;
				case 3: field ='genre'; _field ='genres'; break;
				case 4: field ='misc'; _field ='misc'; break;
				case 5: field ='system'; _field ='systems'; break;
				default: field ='theme'; _field = 'themes'; break;
			}
			for(var i in options_values[field])
			{
				if(count >0 ) s = ","; else s = "";
				if(i>0) temp += s+options_values[field][i];
				count++;
			}
			$('#add_form').append("<input type='hidden' name='"+_field+"_added' value='"+temp+"' />");
		}
		//keywords added end			
	}

	
	function save_new_area(){//add the thematics of the new clic area to the array created for that
		tab_of_areas[clic_areas_nb] = [getSelectValue('theme_select'),getSelectValue('location_select'),getSelectValue('person_select'),getSelectValue('genre_select'),getSelectValue('misc_select'),getSelectValue('system_select'),getSelectValue('direct_link_select')];
	}
	function save_added_area(){//update the thematics of the clic area in the array created for that
		if(last_check >= 0) tab_of_areas[last_check] = [getSelectValue('theme_select'),getSelectValue('location_select'),getSelectValue('person_select'),getSelectValue('genre_select'),getSelectValue('misc_select'),getSelectValue('system_select'),getSelectValue('direct_link_select')];
	}
	function display_new_area(txt){//display the new clic area selected
		$("#clic_area_display").html("Key area : &nbsp;&nbsp;<span style='font-size:1.2em;font-weight:bold;color:#5555ff'> "+txt+" <br></span>");
	}
	function display_added_area(check){//display the clic area selected
		var area = text.substr(tab_of_areas_position[check][0], (tab_of_areas_position[check][1]-tab_of_areas_position[check][0]));
		$("#clic_area_display").html("Key area : &nbsp;&nbsp;<span style='font-size:1.2em;font-weight:bold;color:#5555ff'>"+area+"<br></span>");
	}
	function display_text(){//display 'whole text' after 'click area :'
		$("#clic_area_display").html("Key area : &nbsp;&nbsp;<span style='font-size:1.2em;font-weight:bold;color:#ffffff'> Whole text <br></span>");
	}
	function display_error(){//display a warning if several clic areas are selected simultaneously
		$("#clic_area_display").html("&nbsp;&nbsp;<span style='font-size:1.2em;font-weight:bold;color:#ff5555'> You selected several key areas <br></span>");
	}
	function load_added_area(check){//display again the thematics which have been selected for a clic area
		update_all_select(tab_of_areas[check]);
	}

	//this function will handle the actions according to the states transitions  
	function perform_action(current_state,new_state,check,txt) {
		switch (new_state) {
			case CLIC_AREA.ON_NEW_AREA: 
				switch (current_state) {
					case CLIC_AREA.ON_NEW_AREA: 
						save_new_area();
					break;
					case CLIC_AREA.ON_ADDED_AREA: 
						save_added_area();
					break;
					default: 
						save_principal_keywords();
					break;
				}
				reset_all_select();
				display_new_area(txt);
				break;
				
				
			case CLIC_AREA.ON_ADDED_AREA: 
				switch (current_state) {
					case CLIC_AREA.ON_NEW_AREA: 
						save_new_area();
					break;
					case CLIC_AREA.ON_ADDED_AREA: 
						save_added_area(check);
					break;
					default: 
						save_principal_keywords();
					break;
				}
				load_added_area(check);
				display_added_area(check);
				break;
				
				
			case CLIC_AREA.ON_TEXT: 
				switch (current_state) {
					case CLIC_AREA.ON_NEW_AREA: 
						save_new_area();
						load_principal_keywords();
						display_text();
					break;
					case CLIC_AREA.ON_ADDED_AREA: 
						save_added_area();
						load_principal_keywords();
						display_text();
					break;
					default:
						save_principal_keywords();
					break;
				}
				break;
				
				
			case CLIC_AREA.ON_ERROR: 
				switch (current_state) {
					case CLIC_AREA.ON_NEW_AREA: 
						save_new_area();
						load_principal_keywords();
						display_text();
					break;
					case CLIC_AREA.ON_ADDED_AREA: 
						save_added_area();
						load_principal_keywords();
						display_error();
					break;
					default: 
						save_principal_keywords();
					break;
				}
				break;
				
			default: break;

		}
	}

	//this funtion handle the states transitions according to an action on the text
	function state_transition (textGot) {
		txt = textGot.toString();// transform the select text into a string
		//we calculate the selection position(begin and end)
		var begin = strpos(text,htmlspecialchars(txt,null,null,false),0);
		var end = begin + txt.length ;
		
		//we check if the selection is repeated, it means strpos will return true
		if(strpos(text,htmlspecialchars(txt,null,null,false),(begin+1)) && (txt != '')){
			$('#warning').css("display",'inline-block');
			$('#warning').html("Your selection '"+txt+"' is repeated in the text, please broaden your selection");
			$( "#dialog:ui-dialog" ).dialog( "destroy" );
			$( "#warning" ).dialog({resizable: false,width: 300,modal: true,show: 'slide',hide: 'clip',	buttons: {
					"Ok": function() {
						$( this ).dialog( "close" );
					}
				}
			});
			$( "#warning" ).dialog('open');
		}
		//if the selection isn't repeated
		else{
			//we use area_intersect to know where the selection has been done
			var check = area_intersect(begin, end, tab_of_areas_position);
			if(txt == '')//if it was a click on the text, not a selection (the selection is empty)
			{
					perform_action(state,CLIC_AREA.ON_TEXT,check,txt);//transition
					state = CLIC_AREA.ON_TEXT;//the current state
					$("#clic_area_del_button").slideUp("slow");// no delete
			}
			else //a selection has been done
			{
				
				if(check ==-1)//-1 means we have to register a new click area
				{
					$("#clic_area_del_button").slideUp("slow");// no delete
					perform_action(state,CLIC_AREA.ON_NEW_AREA,check,txt);//transition
					state = CLIC_AREA.ON_NEW_AREA;//the current state
					clic_areas_nb++ ;//we have one more click area
					var shifted_begin = begin + shift(begin,tab_of_areas_position);//this is new begin regarding the previous tag (to color blue) 
					tab_of_areas_position[clic_areas_nb] = [begin,end];//add new positions
					//color the new area blue
					text_visible = text_visible.substr(0, shifted_begin) + tag_color + text_visible.substr(shifted_begin,txt.length) + close_tag + text_visible.substr(shifted_begin+txt.length);
					//and update the visible text
					$("#added_text").html(text_visible);
				}
				else if(check ==-2)//-2 means the selection touched several click areas
				{
					$("#clic_area_del_button").slideUp("slow");//ne delete
					perform_action(state,CLIC_AREA.ON_ERROR,check,txt);//transition
					state = CLIC_AREA.ON_ERROR;//current state
				}		
				else if(check >= 0)// a non negative integer means a click area has been selected 
				{
					$("#clic_area_del_button").slideDown("slow");//delete is possible
					$("#clic_area_del_button").attr("onclick","del_clic_area("+check+");");//if someone click on the delete button, this area (check) will be deleted
					perform_action(state,CLIC_AREA.ON_ADDED_AREA,check,txt);//transition
					state = CLIC_AREA.ON_ADDED_AREA;//current state
					last_check = check;	//this is information necessary for the next state transition
				}
			}
		}
	}


	//these functions clear the thematics selection; when a new area is selected
	function reset_one_select(field){
		var elmt = document.getElementById(field+"_select");
		for(var i=0; i< elmt.options.length; i++)
		{
			elmt.options[i].selected = false;
		}		
	}
	function reset_all_select(){
		reset_one_select("theme");
		reset_one_select("location");
		reset_one_select("person");
		reset_one_select("genre");
		reset_one_select("misc");
		reset_one_select("system");
		reset_one_select("direct_link");		
	}
	
	//to verify an intersection between two segments, then betwen an segemnt and an array of segments
	function intersect(x,y,a,b)	{
		if(a>=y || b<=x) return false; else return true;
	}
	function area_intersect(x,y,tab)	{
		var res = -1;
		var count = 0;
		for(var i in tab)
		{
			if(intersect(x,y,tab[i][0],tab[i][1]))
			{
				res = i;
				count++ ;
			}
		}
		if(count > 1) res = -2;
		return res;
	}
	
	//we must pay attention on the diffence between the original text and the visible text, the visible text has tags to color the click areas blue
	//but the position of a click area is calculated according to the original text, so after the calculate a position (begin and end)
	//we mut calculate the shifted position (shifted_begin) in the visible text, considering tags. This function do it
	function shift(begin,tab_of_position)	{
		var shiftin = 0;
		for(var i in tab_of_position)
		{
			if(tab_of_position[i][1] <= begin) //if the begin is after the end of this area
				shiftin += tag_color.length + close_tag.length;//we add tag lengths
		}
		return shiftin;
	}
	
	//this function will delete a click area
	function del_clic_area (position) 	{
		$("#clic_area_del_button").slideUp();//no more delete button
		//we get the position of the click area we wanna delete
		var begin = tab_of_areas_position[position][0];
		var end = tab_of_areas_position[position][1];
		var shifted_begin = begin + shift(begin,tab_of_areas_position);
		
		//we erase the tags from the visible text, to remove the blue color
		text_visible = text_visible.substr(0, shifted_begin) + text_visible.substr((shifted_begin+tag_color.length),(end-begin)) + text_visible.substr(shifted_begin+close_tag.length+tag_color.length+end-begin);
		//update the visible text
		$("#added_text").html(text_visible);
		//remove the area position and themetics from the arrays
		tab_of_areas_position = tab_of_areas_position.slice(0,position).concat(tab_of_areas_position.slice(position+1,tab_of_areas_position.length));
		tab_of_areas = tab_of_areas.slice(0,position).concat(tab_of_areas.slice(position+1,tab_of_areas.length));
		
		display_text();//display that the area is 'whole text'
		load_principal_keywords();//display text thematics
		
		clic_areas_nb-- ;//one less click area
		
		last_check = -1 ;//the last had been removed
		
		state = CLIC_AREA.ON_TEXT ;//the current state
	}

	
	
	
	
	
	
	
	
	
	
	
	
	