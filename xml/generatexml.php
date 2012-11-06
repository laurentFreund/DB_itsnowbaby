<?php

################################################################################################
/**********************************************************************************************\
|**		Document:					generatexml.php                                          **|
|**		Creation:					March 15, 2012                                           **|
|**		Last modification:			June 27, 2012                                            **|
|**		Authors:					Johanna PHAM, Abdoul aziz SENE                           **|
|**		Project:					It's Now Baby, Industrial project 7, EMSE & LFKs		 **|
|**		Company:					EMSE, Cycle ISMIN, Promo 2013                            **|
|**		Description:				Generate 2 xml files with all needed information from db **|
\**********************************************************************************************/
################################################################################################


function generatexml() { 
	$level = 0;
	if(!($fp = fopen("xml/update_level.txt","r"))){
		return -1;
	}
	else{
		fscanf($fp,"%d",$level);
		fclose($fp);
		$level++;
	}
	
	if(file_exists("xml/xml_inb_medias_1_".$level.".xml") or file_exists("xml/xml_inb_keywords_".$level.".xml")){
		return -2;
	}

	$tab_of_tables = array('inb_medias_1', 'inb_keywords');

	foreach ($tab_of_tables as $table){
		// creating the XML document (DOMDocument object)
		$xml = new DOMDocument('1.0', 'utf-8');
		$xml->formatOutput = true;
		
	    // performing a SQL request
	    if($table == 'inb_keywords')
			$media_fields = '*';
	    else
			$media_fields = 'id, mediatype, title, authors, themes, locations, persons, genre, misc, system, time, link_type, link, text, direct_links, associated_audio, associated_video, associated_text, subtitles, extract_of, extension, key_areas, length, weight, width, height, previous, next, time_begin, time_end';

	    $sql = sprintf("SELECT %s FROM %s",$media_fields,$table);
	    $db_items = mysql_query($sql);
	    if(!($db_items))
			return -3;
	   
		// appending all the children
	    if (mysql_num_rows($db_items) != 0){
		    $items = $xml->createElement($table);
		    while ($db_item = mysql_fetch_assoc($db_items)){
				$item = $xml->createElement($table . "_item");
				foreach($db_item as $key => $value){
					$node = $xml->createElement($key,$value);
					$item->appendChild($node);
				}
				$items->appendChild($item);
			}
	    }
	   
		$xml->appendChild($items);

		// saving the object
		$xml->save("xml/xml_".$table."_".$level.".xml");
		// to save the XML in an XML file
	}
	
	//the file update_level.txt contain the number of the last files
	//we hold the last versions, and the last versions before (for handling competition issues) 
	if(!($fp = fopen("xml/update_level.txt","w"))){
		return -4;
	}
	else{
		fwrite($fp,$level);
		fclose($fp);
		if(file_exists("xml/xml_inb_keywords_".($level-2).".xml"))unlink("xml/xml_inb_keywords_".($level-2).".xml");
		if(file_exists("xml/xml_inb_medias_1_".($level-2).".xml"))unlink("xml/xml_inb_medias_1_".($level-2).".xml");
	}
	
	return 1;
}
?>
