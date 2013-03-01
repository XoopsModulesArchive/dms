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

// inc_search_ft_summary.php
// Gets nearby text to use as a summary for the full text search.

//include_once '../../mainfile.php';

//  Automatically returns text containing the $search_string plus x additional characters.
function dms_search_summary($path_and_file, $search_string, $show_beginning = FALSE)
	{
	global $dms_config, $dmsdb;
	
	$highlight = TRUE;
	if( ($show_beginning == TRUE) || (strlen($search_string) < 2) )
		{
		$search_string = "the";   //  Use "the" as "the" is the most common word in the English language.
		$highlight = FALSE;
		}

//	if($dms_config['adn_enable'] != 1) return(0);
	
	// Get the file size from the file system as the search engine index may be out-of-date.
	$file_size = filesize($path_and_file);

	// Open the file for read (write) and binary
	$handle = fopen($path_and_file,"r+b");

	$offset = 0;
	$eof_flag = FALSE;

	while( (($offset + 1000) <= $file_size) && ($eof_flag == FALSE) )
		{
		$data = fread($handle, 1000);

		if(!feof($handle)) 
			{
			$str_position = stripos($data,$search_string);

			if($str_position > 0 && $str_position < 750)
				{
				//  The string has been found
				fseek($handle,($offset + $str_position) );
				break;
				}
			
			if($offset == ($file_size - 1000)) $eof_flag = TRUE;
			$offset += 500;
			if( ($offset + 1000) > $file_size) $offset = $file_size - 1000;
			fseek($handle,$offset);
			}
		}

	fclose($handle);
	
	return(dms_search_description($data, $search_string, ($str_position +2), $highlight));
	}


function dms_search_description($data, $search_string, $offset, $highlight = TRUE)
	{
	//  This function takes the $data and extracts the description as defined by $descript_start and $descript_end,
	//   and "cleans" it for displaying on the screen.  Additionally, the $search_string is indicated at the end of the function.

	global $dms_config;

	//  Set up the size of the description by defining the starting and ending offsets.
	$descript_start = $offset - $dms_config['search_summary_c_before'];
	$descript_end = $offset + $dms_config['search_summary_c_after'] + strlen($search_string);

	//  Ensure that the boundaries are not exceeded and obtain the raw description.
	$data_length = strlen($data);
	if($descript_start < 0) $descript_start = 0;
	if($descript_end > $data_length) $descript_end = $data_length;

	$source_descript = substr($data,$descript_start,($descript_end-$descript_start));

	//  Remove all but the below allowed characters from the description.
	for($i = 0; $i < strlen($source_descript); $i++)
		{
		if(                                                                                           //  Allowed Characters:
			( (ord($source_descript[$i]) >= 65) && (ord($source_descript[$i]) <= 90) ) ||         //  Upper Case
			( (ord($source_descript[$i]) >= 97) && (ord($source_descript[$i]) <= 122) ) ||        //  Lower Case
			( ord($source_descript[$i]) == 32 ) ||                                                //  Space
			( ord($source_descript[$i]) == 33 ) ||                                                // !
			( ord($source_descript[$i]) == 34 ) ||                                                // "
			( ord($source_descript[$i]) == 45 ) ||                                                // -
			( ord($source_descript[$i]) == 46 )                                                   // .
				)
			{
			$final_descript .= $source_descript[$i];
			}
		}

	//  Remove the first and last word fragment.
	$final_descript = strstr($final_descript," ");
	$final_descript = strrev($final_descript);
	$final_descript = strstr($final_descript," ");
	$final_descript = strrev($final_descript);
	$final_descript = trim($final_descript);

	//  Highlight the original text that was searched for.
	//$highlighted_search_string = strtoupper($search_string);

	if($highlight == TRUE)
		{
		$highlighted_search_string = $search_string;
		$highlighted_search_string = "<font style='BACKGROUND-COLOR: yellow'>".$highlighted_search_string."</font>";
		$final_descript = str_ireplace($search_string,$highlighted_search_string,$final_descript);
		}

	//  Add the "..." before and after the description.
	if(strlen($final_descript) > 2) $final_descript = "...".$final_descript."...";

	//  If there is no summary available, display this.
	if(strlen($final_descript) <=2) $final_descript = "No Summary Available";

	return($final_descript);
	}
