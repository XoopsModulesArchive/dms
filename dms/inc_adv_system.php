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

// inc_dms_adv_system.php

//include_once '../../mainfile.php';

//  Automatically adds the current version number to MS Word documents.  
function dms_adv_system($obj_id)
	{
	global $dms_config, $dmsdb;
	
	$search_string = array();
	
	if($dms_config['adv_enable'] != 1) return(0);
	
	// Obtain the path to the file and the current version number.
	$query = "SELECT obj_owner,current_version_row_id FROM ".$dmsdb->prefix('dms_objects')." WHERE obj_id='".$obj_id."'";
	$result = $dmsdb->query($query,'ROW');
	
	$current_version_row_id = $result->current_version_row_id;
	$obj_owner = $result->obj_owner;
	
	// If the document is located in the document templates folder, exit.  No ADV changes are to be made to templates.
	if($obj_owner == $dms_config['template_root_obj_id']) return(0);
	
	$query  = "SELECT major_version, minor_version, sub_minor_version, file_path, file_size FROM ".$dmsdb->prefix('dms_object_versions')." ";
	$query .= "WHERE row_id='".$current_version_row_id."'";
	$result = $dmsdb->query($query,"ROW");
	
	$file_path = $result->file_path;
	$file_size = $result->file_size;
	$current_version = $result->major_version.$result->minor_version.$result->sub_minor_version;
//print "<br>current_version:  ".$current_version;
	$path_and_file = $dms_config['doc_path']."/".$file_path;
	
	// Get all of the possible version numbers of this file
	$query  = "SELECT major_version, minor_version, sub_minor_version FROM ".$dmsdb->prefix('dms_object_versions')." ";
	$query .= "WHERE obj_id='".$obj_id."'";
	$all_version_numbers = $dmsdb->query($query);
	
	// Determine the strings to search for in the file.
	
	$search_string[0] = dms_adv_system_build_search_string();
//print "<br>search_string[0] =  !".$search_string[0]."!";

	$index = 1;
	while($all_version_numbers_data = $dmsdb->getarray($all_version_numbers))
		{
		$test_ver_num  = $all_version_numbers_data['major_version'];
		$test_ver_num .= $all_version_numbers_data['minor_version'];
		$test_ver_num .= $all_version_numbers_data['sub_minor_version'];
		
		$search_string[$index] = dms_adv_system_build_search_string($test_ver_num);
//print "<br>search_string[".$index."]:  ".$search_string[$index];
		$index ++;
		}
	$max_num_search_strings = $index - 1;   // zero based
//print "<br>max_num_search_strings=".$max_num_search_strings;
		
	// Create the document version number based upon the version mask.
	$final_version_number = $dms_config['adv_mask'];
//print "<br>vn before mask replacement:  ".$final_version_number;
	$final_version_number = str_replace($dms_config['adv_mask_char'][0],'0',$final_version_number);
//print "<br>vn after mask replacement:  ".$final_version_number;
	
	// Insert the new (final) version number into the version number mask.
	$current_version_pointer = strlen($current_version) - 1;
	for($index = strlen($final_version_number); $index>=0; $index--)
		{
		if($current_version_pointer < 0) break;
		
		if($final_version_number[$index] == '0') //$dms_config['adn_mask_char'][0])
			{
			$final_version_number[$index] = $current_version[$current_version_pointer];
			$current_version_pointer--;
			}
		}
//print "<BR>Final Version Number:  ".$final_version_number;
	
	// Open the file for read (write) and binary
	$handle = fopen($path_and_file,"r+b");

	$offset = 0;
	$eof_flag = FALSE;
	//while(!feof($handle))
	while( (($offset + 1000) <= $file_size) && ($eof_flag == FALSE) )
		{
		$data = fread($handle, 1000);
//print "<BR>Offset:  ".$offset."<BR>";
		if(!feof($handle)) 
			{
			
			$search_index = 0;
			while($search_index <= $max_num_search_strings)
				{
//print $search_index;
				$str_position = strpos($data,$search_string[$search_index]);
				if($str_position > 0 && $str_position < 750)
					{
					//  The document version number has been found
//print "<BR>Mask Offset:  ".($offset + $str_position +2);
					fseek($handle,($offset + $str_position + 2) );
					fwrite($handle,$final_version_number,strlen($dms_config['adv_mask']));
//print "<BR>Write Document Version Number";
//print "<BR>Search String Found:  !".$search_string[$search_index]."!";
					break;
					}
				
				$search_index ++;
				}

			if($offset == ($file_size - 1000)) $eof_flag = TRUE;
			$offset += 500;
			if( ($offset + 1000) > $file_size) $offset = $file_size - 1000;
			fseek($handle,$offset);
			}
		}

	fclose($handle);


//print "<BR>END";
//exit(0);
	}
	
function dms_adv_system_build_search_string($ver_num = "")
	{
	global $dms_config;
	
	if(0 == strlen($ver_num)) $mask = $dms_config['adv_mask'];
	else
		{
		// If a version number is specified, combine the version number and the mask to create a new mask to use in the $search_string.
		
		$mask = $dms_config['adv_mask'];
//print "<br>mask before replacement:  ".$mask;
		$mask = str_replace($dms_config['adv_mask_char'][0],'0',$mask);
//print "<br>mask after replacement:  ".$mask;
		
		// Insert the version number into the version number mask.
		$ver_num_pointer = strlen($ver_num) - 1;
		for($index = strlen($mask); $index>=0; $index--)
			{
			if($ver_num_pointer < 0) break;
			
			if($mask[$index] == '0') 
				{
				$mask[$index] = $ver_num[$ver_num_pointer];
				$ver_num_pointer--;
				}
			}
//print "<BR>Final Mask:  ".$mask;
		}
	
	$search_string_len = strlen($mask);
	
	$search_string[0] = "\x00";
	$search_string[1] = "\x00";
	for($index = 2; $index <= ($search_string_len + 1); $index++)
		{
		$search_string[$index] = $mask[($index - 2)];
		}
	$index = $search_string_len + 2;
	$search_string[$index] = "\x00";
	
	$search_string_len += 3;
	
	$search_string = implode("",$search_string);
	
//print "<br>ss:  !".$search_string."!";
//print "<br>ssl:  ".$search_string_len;
	
	return $search_string;
	}