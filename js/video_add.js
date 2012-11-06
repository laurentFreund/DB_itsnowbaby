	//Some global variables
	var edit = false;
	
	
	$( "#bar" ).progressbar();//activate the progressbar function
	
	
	//after setting all thematics, we must put all information in inputs before submitting the form.
	//This function generate all inputs and put in information
	//the keywords are set from a multiple select field. this function transform selected keywords into strings and put them into to hiddden html inputs
	//so after submit, we get strings back through $_POST variables
	function generate_inputs()	{ 
		var count = 0;
		var temp = "";
		var s = "";
		
		//archive_keywords
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
		//archive_keywords end
		
		//archive_direct_links
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
		//text_direct_links end
		
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
	
	
	
	
	
	
	
	

	