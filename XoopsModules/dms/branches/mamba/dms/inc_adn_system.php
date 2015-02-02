<?php
//  ------------------------------------------------------------------------ //
//                     Document Management System                            //
//                                                                           //
//                                                                           //
// ------------------------------------------------------------------------- //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //

// inc_dms_adn_system.php

//include_once '../../mainfile.php';

//  Automatically adds the object id as a document number to MS Word documents.  
//  Also will add the same document number to a defined document property,  
function dms_adn_system($obj_id)
	{
	global $dms_config, $dmsdb;
	
	if($dms_config['adn_enable'] != 1) return(0);
	
	// Obtain the path to the file and the obj_owner.
	$query = "SELECT obj_owner, current_version_row_id FROM ".$dmsdb->prefix('dms_objects')." WHERE obj_id='".$obj_id."'";
	$result = $dmsdb->query($query,'ROW');
	
	$current_version_row_id = $result->current_version_row_id;
	$obj_owner = $result->obj_owner;
	
	// If the doucment is located in the document templates folder, exit.  No ADN changes are to be made to templates.
	if($obj_owner == $dms_config['template_root_obj_id']) return(0);
	
	$query = "SELECT file_path,file_size FROM ".$dmsdb->prefix('dms_object_versions')." WHERE row_id='".$current_version_row_id."'";
	$result = $dmsdb->query($query,'ROW');
	
	$file_path = $result->file_path;
	$file_size = $result->file_size;
	
	$path_and_file = $dms_config['doc_path']."/".$file_path;
	
	// Determine the string to search for in the file.
/*
	$search_string_len = strlen($dms_config['adn_mask']);
	
	$search_string[0] = "\x00";
	$search_string[1] = "\x00";
	for($index = 2; $index <= ($search_string_len + 1); $index++)
		{
		$search_string[$index] = $dms_config['adn_mask'][($index - 2)];
		}
	$index = $search_string_len + 2;
	$search_string[$index] = "\x00";
	
	$search_string_len += 3;
	
	$search_string = implode("",$search_string);
*/
$search_string = $dms_config['adn_mask'];                 //////////////////////////////////////////////////////////////!!!!!!!!!!!!!!

//print "<br>ss:  !".$search_string."!";
//print "<br>ssl:  ".$search_string_len;

	// Create the document ID based upon the document mask.
	$str_obj_id = sprintf("%u",$obj_id);
	$document_number = $dms_config['adn_mask'];
//print "<br>dn before mask replacement:  ".$document_number;
	$document_number = str_replace($dms_config['adn_mask_char'][0],'0',$document_number);
//print "<br>dn after mask replacement:  ".$document_number;
	
	// Place the object id number in the document number mask.
	$str_obj_id_pointer = strlen($str_obj_id) - 1;
	for($index = strlen($document_number); $index>=0; $index--)
		{
		if($str_obj_id_pointer < 0) break;
		
		if($document_number[$index] == '0') //$dms_config['adn_mask_char'][0])
			{
			$document_number[$index] = $str_obj_id[$str_obj_id_pointer];
			$str_obj_id_pointer--;
			}
		}
//print "<BR>Final Document Number:  ".$document_number;
	
	// Open the file for read (write) and binary
	$handle = fopen($path_and_file,"r+b");

	$offset = 0;
	$eof_flag = FALSE;
	//while(!feof($handle))
	while( (($offset + 1000) <= $file_size) && ($eof_flag == FALSE) )
		{
		$data = fread($handle, 1000);
//print "<BR>Offset:  ".$offset;
		if(!feof($handle)) 
			{
			//$str_position = strpos($data,$search_string);    Old PHP native function to find mask.
			$str_position = dms_pattern_pos($data,$search_string,$dms_config['adn_mask_char'][0]);

			if($str_position > 0 && $str_position < 750)
				{
				//  The document number has been found
//print "<BR>Mask Offset:  ".($offset + $str_position +2);
				//fseek($handle,($offset + $str_position + 2) ); // Used when expecting 00 00 before the mask.
				fseek($handle,($offset + $str_position) );
				fwrite($handle,$document_number,strlen($dms_config['adn_mask']));
//print "<BR>Write Document Number";
				}
			
			if($offset == ($file_size - 1000)) $eof_flag = TRUE;
			$offset += 500;
			if( ($offset + 1000) > $file_size) $offset = $file_size - 1000;
			fseek($handle,$offset);
			}
		}

	fclose($handle);

	// If the ADN Optional Properties Field is set, set the property to the ADN.
	if($dms_config['adn_prop_field'] != '-1')
		{
		$query  = "UPDATE ".$dmsdb->prefix('dms_object_properties')." ";
		$query .= "SET property_".$dms_config['adn_prop_field']."='".$document_number."' ";
		$query .= "WHERE obj_id='".$obj_id."'";
		$dmsdb->query($query);
		}
//print "<BR>END";
//exit(0);
	}

function dms_pattern_pos($data,$search_pattern,$wild_card)
	{
	//  Two stage pattern matching function
	//  This function searches for the $search_pattern in the $data.  The $wild_card is a character in the search pattern
	//  that is ignored.  For example dms_pattern_pos($data,"dms-000-000-000-000","0" will search for dms-???-???-???-??? and
	//  return the starting position of the pattern, if found.  The $wild_card is also the mask character.
	//  If no pattern is found, FALSE is returned.
	$beginning_search_character = $search_pattern[0];
	$match_counter = 0;

	for($index = 0; $index <= strlen($data); $index++)
		{
		$match_counter = 1;
		if($data[$index] == $beginning_search_character)
			{
			// The beginning character has been found.  Now verify that the rest of the characters match the search pattern.
			$data_index = $index + 1;
			for($pattern_index = 1; $pattern_index <= strlen($search_pattern); $pattern_index++)
				{
				if( ($search_pattern[$pattern_index] == $wild_card) 
					|| ($data[$data_index] == $search_pattern[$pattern_index]) )
						$match_counter ++;
				else 
					break;

				$data_index++;
				}
			}

		//  If the entire pattern matches, return the offset location.
		if($match_counter == strlen($search_pattern)) return $index;
		}
	}