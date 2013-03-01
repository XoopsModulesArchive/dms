<?php 
//  ------------------------------------------------------------------------ //
//                     Document Management System                            //
//                  Written By:  Brian E. Reifsnyder                         //
//                        Copyright 11/16/2005                                //
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

// Properties Search Function
// search_prop.php


include '../../mainfile.php';

include_once 'inc_dms_functions.php';
include XOOPS_ROOT_PATH.'/header.php';

$folders = array();
$folders_index = 0;

function get_date($var_name, $current_value = -1)
	{
// If there is a current_value, convert it into the appropriate information
	if($current_value != -1)
		{
		$month  = (int)strftime("%m",$current_value);
		$day    = (int)strftime("%d",$current_value);
		$year   = (int)strftime("%Y",$current_value);
		}
		
// Get Month
	print "<select name='slct_".$var_name."_month'>\r";
	for($index = 1;$index <= 12; $index++)
		{
		$selected = "";
		if( ($current_value != -1) && ($index == $month) ) $selected = "SELECTED";
		print "  <option ".$selected.">".$index."</option>\r";
		}
	print "</select>\r";

	print "/&nbsp;";
	
// Get Day
	print "<select name='slct_".$var_name."_day'>\r";
	for($index = 1;$index <= 31; $index++)
		{
		$selected = "";
		if( ($current_value != -1) && ($index == $day) ) $selected = "SELECTED";
		print "  <option ".$selected.">".$index."</option>\r";
		}
	print "</select>\r";

	print "/&nbsp;";
	
// Get Year
	print "<select name='slct_".$var_name."_year'>\r";
	for($index = 2006;$index <= 2020; $index++)
		{
		$selected = "";
		if( ($current_value != -1) && ($index == $year) ) $selected = "SELECTED";
		print "  <option ".$selected.">".$index."</option>\r";
		}
	print "</select>\r";
	}


function get_folders($current_folder)
	{
	global $dmsdb,$folders,$folders_index;

	$folders[$folders_index++] = $current_folder;
		
	// Get the objects in the specified folder.
	$query  = "SELECT * FROM ".$dmsdb->prefix("dms_objects")." ";
	$query .= "WHERE obj_owner='".$current_folder."' AND obj_type='".FOLDER."' ";
	
	//print "<BR>".$query;

	$result = $dmsdb->query($query);
	$num_rows = $dmsdb->getnumrows();
	
	if ($num_rows > 0)
		{
		while($result_data = $dmsdb->getarray($result))
			{
			get_folders($result_data['obj_id']);
			}
		}
	}
	
//include_once XOOPS_ROOT_PATH."/modules/dms/inc_dms_functions.php";

//$search_query = $HTTP_POST_VARS['txt_search_query'];
$search_query = dms_get_var("txt_search_query");

print "<table width='100%'>\r";
//print "  <tr>\r";
  
// Beginning of Search Selection Section

//print "    <td valign='top'>\r";
//print "      <table width='100%'>\r";
dms_display_header(1);
//print "      </table>\r";
  
print "      <BR>\r";

//print "<table width='100%'>\r";

print "  <tr>\r";
print "    <td width='100%' ".$dms_config['class_content'].">\r";
print "      <b>Properties Search:</b>\r";
print "    </td>\r";
print "  </tr>\r";

print "  <tr><td><BR></td></tr>\r";

print "  <tr>\r";
print "    <td>\r";

print "<form name='frm_prop_search' method='post' action='search_prop.php'>\r";
// Display properties for search
//print "<table>\r";

$slct_srch_prop_name_option[1]="";
$slct_srch_prop_name_option[2]=" SELECTED";
$slct_srch_prop_name_option[3]="";

$var_property_name = "";

$slct_srch_doc_owner_option = "";

$slct_srch_create_date_option[0] = " SELECTED";
$slct_srch_create_date_option[1] = "";
$slct_srch_create_date_option[2] = "";
$slct_srch_create_date_option[3] = "";

$search_create_date_type = 0;

$search_time_stamp = -1;

$txt_property_name = "";

if (dms_get_var("hdn_reload_flag") == "TRUE" ) 
	{
	$slct_srch_prop_name_option[2]="";
	$slct_srch_prop_name_option[dms_get_var("slct_srch_property_name")] = " SELECTED";
	
	$txt_property_name = dms_get_var("txt_property_name");
	
	$slct_srch_doc_owner_option = dms_get_var("slct_srch_doc_owner");
	
	$slct_srch_create_date_option[0] = "";
	$slct_srch_create_date_option[dms_get_var("slct_srch_create_date")] = " SELECTED";
	
	$search_create_date_type = dms_get_var("slct_srch_create_date");
	
	$search_time_stamp = mktime(0,0,0,dms_get_var("slct_srch_create_date_month"),dms_get_var("slct_srch_create_date_day"),dms_get_var("slct_srch_create_date_year"));
	}

// Document Name
	
print "        <tr>\r";
print "          <td align='left'>\r";
print "            " . _DMS_NAME . ":<BR>";
print "            &nbsp;&nbsp;&nbsp;";

print "              <select name='slct_srch_property_name'>\r";
print "                <option value='1' ".$slct_srch_prop_name_option[1].">" . _DMS_OPTION_IS . "</option>\r";
print "                <option value='2' ".$slct_srch_prop_name_option[2].">". _DMS_OPTION_CONTAINS . "</option>\r";
print "                <option value='3' ".$slct_srch_prop_name_option[3].">" . _DMS_OPTION_STARTS . "</option>\r";
print "              </select>&nbsp;\r";
print "            &nbsp;&nbsp;&nbsp;";
print "            <input type='text' name='txt_property_name' value='".$txt_property_name."' size='60' maxlength='250' ".$dms_config['class_content'].">\r";

print "          </td>\r";
print "        </tr>\r";

// Document Properties

for ($index = 0; $index <=9; $index++)
	{
	$query = 'SELECT data FROM '.$dmsdb->prefix("dms_config")." WHERE name='property_".$index."_name'";
	$prop_name = $dmsdb->query($query,'data');
	
	if (strlen($prop_name) > 1)
    	{
		$prop_used[$index]="TRUE";
	
		$selected_option[1] = "";
		$selected_option[2] = "";
		$selected_option[3] = "";

		$query  = "SELECT row_id,select_box_option FROM ".$dmsdb->prefix("dms_object_properties_sb")." ";
		$query .= "WHERE property_num='".$index."' ";
		$query .= "ORDER BY disp_order";
		$options  = $dmsdb->query($query);
		$num_db_rows = $dmsdb->getnumrows();
	    
		if (dms_get_var("hdn_reload_flag") == "TRUE")
			{
			switch ($index)
				{
				case 0:  $data[0]=dms_get_var("var_property_0");  $option[0]=dms_get_var("slct_srch_property_0");  break;
				case 1:  $data[1]=dms_get_var("var_property_1");  $option[1]=dms_get_var("slct_srch_property_1");  break;
				case 2:  $data[2]=dms_get_var("var_property_2");  $option[2]=dms_get_var("slct_srch_property_2");  break;
				case 3:  $data[3]=dms_get_var("var_property_3");  $option[3]=dms_get_var("slct_srch_property_3");  break;
				case 4:  $data[4]=dms_get_var("var_property_4");  $option[4]=dms_get_var("slct_srch_property_4");  break;
				case 5:  $data[5]=dms_get_var("var_property_5");  $option[5]=dms_get_var("slct_srch_property_5");  break;
				case 6:  $data[6]=dms_get_var("var_property_6");  $option[6]=dms_get_var("slct_srch_property_6");  break;
				case 7:  $data[7]=dms_get_var("var_property_7");  $option[7]=dms_get_var("slct_srch_property_7");  break;
				case 8:  $data[8]=dms_get_var("var_property_8");  $option[8]=dms_get_var("slct_srch_property_8");  break;
				case 9:  $data[9]=dms_get_var("var_property_9");  $option[9]=dms_get_var("slct_srch_property_9");  break;
				}
	  
			$selected_option[$option[$index]]=" SELECTED";
			}
		else
			{
			$data[$index] = "";
			$selected_option[2] = " SELECTED";
			}

		print "        <tr>\r";
		print "          <td align='left'>\r";
		print "            ".$prop_name.":<BR>";
		print "            &nbsp;&nbsp;&nbsp;";
    
		if ($num_db_rows == 0)
			{
			print "              <select name='slct_srch_property_".$index."'>\r";
			print "                <option value='1' ".$selected_option[1].">" . _DMS_OPTION_IS . "</option>\r";
			print "                <option value='2' ".$selected_option[2].">" . _DMS_OPTION_CONTAINS  . "</option>\r";
			print "                <option value='3' ".$selected_option[3].">" . _DMS_OPTION_STARTS  . "</option>\r";
			print "              </select>&nbsp;\r";
			print "            &nbsp;&nbsp;&nbsp;";
			print "            <input type='text' name='var_property_".$index."' value='".$data[$index]."' size='60' maxlength='250' ".$dms_config['class_content'].">\r";
			}
		else
			{
			print "              <select name='var_property_".$index."' ".$class_content.">\r";
			print "              <option value=''>Do Not Search</option>\r";
	    
			while($option_data = mysql_fetch_array($options))
				{
				print "              <option value='".$option_data['row_id']."'";
				if ($data[$index] == $option_data['row_id']) print " SELECTED";
				print ">".$option_data['select_box_option']."</option>\r";
				}
	  
			print "              </select>\r";
	  
			print "              <input type='hidden' name='slct_srch_property_".$index."' value='1'>\r";    
			}
	  
		print "          </td>\r";
		print "        </tr>\r";
		}
	else
		{
		$prop_used[$index]="FALSE";
		}
	}

// Document Owner
print "        <tr>\r";
print "          <td align='left'>\r";
print "            Owner:<BR>";
print "            &nbsp;&nbsp;&nbsp;";

$query = "SELECT uid,uname from ".$dmsdb->prefix("users")." ORDER BY uname";
$result = $dmsdb->query($query);
  
print "\r<select name='slct_srch_doc_owner'>\r";

print "  <option value='0'>Anyone</option>\r";

while($result_data = $dmsdb->getarray($result))
	{
	print "  <option value='".$result_data['uid']."' ";
	if ($slct_srch_doc_owner_option == $result_data['uid']) print " SELECTED";
	print ">".$result_data['uname']."</option>\r";
	}
print "</select>\r";

print "          </td>\r";
print "        </tr>\r";

// Document Creation Time
print "        <tr>\r";
print "          <td align='left'>\r";
print "            Created:<BR>";
print "            &nbsp;&nbsp;&nbsp;";

print "              <select name='slct_srch_create_date'>\r";
print "                <option value='0' ".$slct_srch_create_date_option[0].">Not Applicable</option>\r";
print "                <option value='1' ".$slct_srch_create_date_option[1].">On</option>\r";
print "                <option value='2' ".$slct_srch_create_date_option[2].">Before</option>\r";
print "                <option value='3' ".$slct_srch_create_date_option[3].">After</option>\r";
print "              </select>&nbsp;\r";
print "            &nbsp;&nbsp;&nbsp;";
get_date("srch_create_date", $search_time_stamp);

print "          </td>\r";
print "        </tr>\r";

// Check box for limiting search to folder and sub-folders
//if ($HTTP_POST_VARS['chk_folder_limit']) $chk_folder_limit = "CHECKED";

$chk_folder_limit = dms_get_var("chk_folder_limit");
if($chk_folder_limit == FALSE) $chk_folder_limit = "";
else $chk_folder_limit = "CHECKED";

print "        <tr><td><BR></td></tr>\r";

print "        <tr>\r";
print "          <td align='left'>\r";
print "            Limit Search to Active Folder and Sub-Folders:&nbsp;&nbsp;&nbsp;<input type='checkbox' name='chk_folder_limit' ".$chk_folder_limit.">";
print "          </td>\r";
print "        </tr>\r";

print "        <tr><td><BR></td></tr>\r";

print "        <tr>\r";
print "          <td align='left'>\r";
print "            <input type='submit' name='btn_search' value='" . _DMS_SEARCH . "' ".$dms_config['class_content'].">\r";  
print "            <input type='button' name='btn_exit' value='" . _DMS_EXIT . "' onclick='location=\"index.php\";'>\r";
print "          </td>\r";
print "        </tr>\r";

print "        <input type='hidden' name='hdn_reload_flag' value='TRUE'>\r";
print "        </form>\r";


// End Of Search Selection Section

if (dms_get_var("hdn_reload_flag") == "TRUE")
	{
	//  If applicable, determine the active folder and it's sub-folders
	$active_folder = dms_active_folder();
	if( ($chk_folder_limit == "CHECKED") && ($active_folder != 0) ) get_folders($active_folder);
	
//for($index = 0;$index < $folders_index; $index++) print "<BR>".$folders[$index]." [".$index."]"; 
	
	print "        <tr><td><BR></td></tr>\r";

	// Get the search_limit to limit the search to X number of entries
	$search_limit = $dms_config['search_limit'];

	// Create SQL Query
	$ps_query  = "SELECT ".$dmsdb->prefix("dms_object_properties").".obj_id, obj_name, misc_text, user_id, time_stamp_create ";
	$ps_query .= "FROM ".$dmsdb->prefix("dms_object_properties")." ";
	$ps_query .= "INNER JOIN ".$dmsdb->prefix("dms_objects")." ";
	$ps_query .= "ON ".$dmsdb->prefix("dms_object_properties").".obj_id ";
	$ps_query .= "= ".$dmsdb->prefix("dms_objects").".obj_id ";
	$ps_query .= "INNER JOIN ".$dmsdb->prefix("dms_object_perms")." ";
	$ps_query .= "ON ".$dmsdb->prefix("dms_object_properties").".obj_id ";
	$ps_query .= "= ".$dmsdb->prefix("dms_object_perms").".ptr_obj_id ";
	$ps_query .= "WHERE ";
	
	$and_flag = "FALSE";

	if(strlen($txt_property_name)>=1)
		{
		$and_flag = "TRUE";
	
		//$ps_query .= "WHERE ";

		switch (dms_get_var("slct_srch_property_name"))
			{
			case 1:
				$ps_query .= "(obj_name = '".$txt_property_name."') ";
				$and_flag = "TRUE";
				break;
			case 2:
				$ps_query .= "(obj_name LIKE '%".$txt_property_name."%') ";
				$and_flag = "TRUE";
				break;
			case 3:
				$ps_query .= "(obj_name LIKE '".$txt_property_name."%') ";
				$and_flag = "TRUE";
				break;
			}
		}
  
	for ($index = 0; $index <= 9; $index++)
    		{
		if ( ($prop_used[$index]=="TRUE" )
		 &&  (strlen($data[$index])>=1) )
    			{
			//if ($and_flag == "FALSE") $ps_query .= "WHERE ";
			if ($and_flag == "TRUE")  $ps_query .= "AND ";
	  
			switch ($option[$index])
				{
				case 1:
					$ps_query .= "(property_".$index." = '".$data[$index]."') ";
					$and_flag = "TRUE";
					break;
				case 2:
					$ps_query .= "(property_".$index." LIKE '%".$data[$index]."%') ";
					$and_flag = "TRUE";
					break;
				case 3:
					$ps_query .= "(property_".$index." LIKE '".$data[$index]."%') ";
					$and_flag = "TRUE";
					break;
				}
			}
		}

	if ($slct_srch_doc_owner_option > 0)
		{
		if ($and_flag == "TRUE") $ps_query .= "AND ";
		$ps_query .= " user_id='".$slct_srch_doc_owner_option."' ";
		$and_flag = "TRUE";
		}

	if($search_create_date_type > 0)
		{
		if ($and_flag == "TRUE") $ps_query .= "AND ";
		$and_flag = "TRUE";
		
		switch($search_create_date_type)
			{
			case 1:
				$num_seconds_in_day = 24 * 60 * 60;
			
				$ps_query .= " (time_stamp_create > '".$search_time_stamp."' ";
				$ps_query .= " AND";
				$ps_query .= " time_stamp_create < '".($search_time_stamp + $num_seconds_in_day)."') ";
				break;
			case 2:
				$ps_query .= " time_stamp_create < '".$search_time_stamp."' ";
				break;
			case 3:
				$ps_query .= " time_stamp_create > '".$search_time_stamp."' ";
				break;
			}
		}
		
	// If the search is limited to the active folder and it's sub-folders
	if( ($chk_folder_limit == "CHECKED") && ($active_folder != 0)  )
		{
		if ($and_flag == "TRUE") $ps_query .= "AND ";
		
		$ps_query .= "(";
		
		for($index = 0;$index < $folders_index; $index++)
			{
			if($index > 0) $ps_query .= "OR ";
			
			$ps_query .= "obj_owner='".$folders[$index]."' ";
			}
		
		$ps_query .= ") ";
		
		$and_flag = "TRUE";
		}
	
	if ($and_flag == "TRUE") $ps_query .= "AND ";
	$ps_query .= " obj_status < 2 AND user_perms='4'";
	
	$ps_query .= " ORDER BY obj_name ";
	$ps_query .= " LIMIT ".$search_limit; 

//print $ps_query;
      
	$result = $dmsdb->query($ps_query);
	$num_rows = $dmsdb->getnumrows();
  
	if ($num_rows > 0)
		{
		print "  <tr>\r";
		print "    <td>\r";
//		print "      <table>\r";
		print "        <tr>\r";
		print "          <td align='left' width='75%'><b>Documents:</b></td>\r";
		//print "          <td><b>" . _DMS_VERSION . "</b></td>\r";
		print "        </tr>\r";
	
		while($result_data = $dmsdb->getarray($result))
			{
	    
			// Permissions required to view this object:
    		//  BROWSE, READONLY, EDIT, OWNER
			$perms_level = dms_perms_level($result_data['obj_id']);
			$perms_level = dms_determine_admin_perms($perms_level);
		
			if ( ($perms_level == 1) || ($perms_level == 2) || ($perms_level == 3) || ($perms_level == 4) )
				{
				print "  <tr>\r";
				print "    <td align='left'>  <!-- ".$result_data['obj_id']." -->\r";

				$misc_text = $result_data['misc_text'];
				if (strlen($misc_text) >0)
					{
					$misc_text = "&nbsp;&nbsp;&nbsp;(".$misc_text.")";
					}
				else $misc_text = "";
				
				if ($dms_anon_flag == 1)   // If the user is anonymous, provide at most browse or read-only access.
					{    
					switch ($perms_level)
						{
						case 1:
							print "&nbsp;&nbsp;&nbsp;".$result_data['obj_name'].$misc_text;
							break;
						case 2:
							print "&nbsp;&nbsp;&nbsp;<a href='#' onclick='javascript:void(window.open(\"file_options.php?obj_id=".$result_data['obj_id']."\"))'>".$result_data['obj_name'].$misc_text."</a>\r";
							break;
						default:
						print _DMS_ACCESS_DENIED ;
						}
					}
				else
					{
//					if ( ($perms_level == 1) || ($perms_level == 2) || ($perms_level == 3) || ($perms_level == 4) )
//						{
						print "&nbsp;&nbsp;&nbsp;<a href='#' onclick='javascript:void(window.open(\"file_options.php?obj_id=".$result_data['obj_id']."\"))'>".$result_data['obj_name'].$misc_text."</a>\r";
//						}
					}
			
				print "    </td>\r";
				print "  </tr>\r";
				}
			}
	
		if($num_rows >= $search_limit)
			{    
			// Documents listed exceed limits
			print "  <tr><td>\r";
			print "        <BR>" . _DMS_BECAUSE_AT_LEAST . $search_limit. _DMS_DOCS_EXCEED_LIMIT;
			print "        " . _DMS_REFINE_PARAMETERS;
			print "  </td></tr>\r";
			}
		
//			print "      </table>\r";
//			print "    </td>\r";
//			print "  </tr>\r";
		}
	else
		{
		print "<tr><td><b>" . _DMS_NO_FILES_FOUND  . "</b><br></td></tr>"; 
		}
	}
    
//print "      </table>\r";

//print "    </td>\r";
//print "  </tr>\r";
print "</table>\r";
 
include_once XOOPS_ROOT_PATH.'/footer.php';
?> 
 
 
