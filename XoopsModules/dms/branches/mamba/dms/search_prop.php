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
include_once 'inc_search_summary.php';
//include XOOPS_ROOT_PATH.'/header.php';
include 'inc_pal_header.php';


///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


$page = dms_get_var("hdn_page");
if( !(($page > 0) && ($page < 100)) ) $page == FALSE;

if($page == FALSE) dms_clear_search_results();


function dms_clear_search_results()
	{
	$_SESSION['dms_search_results'] = "";
	}

function dms_display_search_results($page = 1)
	{
	global $dms_config, $search_query, $last_query;

	if($page == FALSE) $page = 1;

	$table_header_flag = FALSE; 
	$disp_nff_flag = TRUE;

	$results_per_page = $dms_config['search_results_per_page'];
	$total_number_pages = ceil($_SESSION['dms_search_results']['total_results']/$results_per_page);

	$start = $results_per_page * $page - $results_per_page;
	$end = $results_per_page * $page - 1;
	if($end >= $_SESSION['dms_search_results']['total_results']) $end = $_SESSION['dms_search_results']['total_results'] - 1;

	for($i = $start; $i <= $end; $i++)
		{
		$disp_nff_flag = FALSE;

		if ($table_header_flag == FALSE)
			{
			$table_header_flag = TRUE;

			if($_SESSION['dms_search_results']['total_results'] > $results_per_page)
				{
				print "  <tr>\r";
				print "    <td colspan='2'></td>\r";
				print "    <td align='right'>";

				print "Page:&nbsp;&nbsp;";

				for($p_index = 1; $p_index <= $total_number_pages; $p_index++)
					{
					if($p_index == $page)
						{
						print "&nbsp;<b>".$p_index."</b>&nbsp;";
						}
					else
						{
						print "&nbsp;<a onclick=\"set_page(".$p_index.");\">".$p_index."</a>&nbsp;";
						}
					}

				print "    </td>\r";
				print "  </tr>\r";
				}


			print "  <tr>\r";
			print "    <td width='100%' colspan='3' align='left'><b>Document(s):</b></td>\r";
			print "  </tr>\r";
			}

		print "  <tr>\r";
		print "    <td width='3%'></td>\r";
		print "    <td align='left' colspan='2'>  <!-- ".$_SESSION['dms_search_results']['obj_id'][$i]." -->\r";

		if($_SESSION['dms_search_results']['disp'][$i] == "D_OPTIONS")
			{
			print "      <a href='#' onclick='javascript:void(window.open(\"file_options.php?obj_id=".$_SESSION['dms_search_results']['obj_id'][$i]."\"))'>".$_SESSION['dms_search_results']['obj_name'][$i]."</a>\r";
			}
		else
			{
			print $_SESSION['dms_search_results']['obj_name'][$i]."\r";
			}

		print "    </td>\r";
		
		print "  </tr>\r";

		if($dms_config['search_summary_flag'] == 1)
			{
			print "  <tr>\r";
			print "    <td></td><td width='3%'></td>\r";
			print "    <td align='left'>";

			if(strlen($_SESSION['dms_search_results']['summary'][$i]) < 2);
				{
				$_SESSION['dms_search_results']['summary'][$i] 
					= dms_search_summary($_SESSION['dms_search_results']['path_and_file'][$i],"",TRUE);
				}

			print $_SESSION['dms_search_results']['summary'][$i];

			print "    </td>\r";
			print "    <td colspan='2'></td>\r";
			print "  </tr>\r";
			}
		}

	if ($disp_nff_flag == TRUE) print "<tr><td colspan='2'><b>No files have been found that match your query.</b><br></td></tr>"; 
	}

function dms_store_search_results($obj_id,$obj_name,$disp)
	{
	if(isset($_SESSION['dms_search_results']['total_results']))
		{
		$index = $_SESSION['dms_search_results']['total_results'];
		}
	else
		{
		$_SESSION['dms_search_results']['total_results'] = 0;
		$index = 0;
		}

	$file_props = dms_get_rep_file_props($obj_id);
	$full_path_and_file = $file_props['file_path'];

	$_SESSION['dms_search_results']['obj_id'][$index] = $obj_id;
	$_SESSION['dms_search_results']['obj_name'][$index] = $obj_name;
	$_SESSION['dms_search_results']['summary'][$index] = "";
	$_SESSION['dms_search_results']['path_and_file'][$index] = $full_path_and_file;
	$_SESSION['dms_search_results']['disp'][$index] = $disp;

	$_SESSION['dms_search_results']['total_results']++;
	}


////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

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
	

print "<script language='JavaScript'>\r";
print "<!--\r";

print "function set_page(page_num)\r";
print "  {\r";
print "  document.frm_prop_search.hdn_page.value = page_num;\r";
print "  document.frm_prop_search.submit();\r";
print "  }\r";

print "// -->\r";
print "</script>\r";


print "<table width='100%'>\r";

// Beginning of Search Selection Section

dms_display_header(1);
  
print "      <BR>\r";

/*
if ($skip == 1)
	{
	print "        <tr>\r";
	print "          <td width='100%' ".$dms_config['class_subheader']." align='left'>\r";
	print "            <b>Full Text Search:</b>\r";
	print "          </td>\r";
	print "        </tr>\r";
	
	print "        <tr>\r";
	print "          <td align='left'>\r";

	print "      <form name='frm_ft_search' method='post' action='search_ft.php'>\r"; 
	
	print "            <br>\r";
	//begin search box
	print "              <input type='text' name='txt_search_query' value='' size='60' maxlength='250' ".$dms_config['class_content'].">\r";

	print "              <BR><BR>\r";  
	print "              <input type='submit' name='btn_search' value='Search' ".$dms_config['class_content'].">\r"; 
	// end search box 
	
	print "<BR><BR><BR>\r";
	
	print "          </td>\r";
	print "        </tr>\r";
	print "      </form>\r";
	}
*/

print "  <tr>\r";
print "    <td width='100%' ".$dms_config['class_content']." align='left'>\r";
print "      <b>Properties Search:</b>\r";
print "    </td>\r";
print "  </tr>\r";

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

if (dms_get_var("hdn_reload_flag") == "TRUE") 
	{
	$slct_srch_prop_name_option[2]="";
	$slct_srch_prop_name_option[dms_get_var("slct_srch_property_name")] = " SELECTED";
	
	$search_type = dms_get_var("slct_srch_property_name");
	
	$txt_property_name = dms_get_var("txt_property_name");
	
	$slct_srch_doc_owner_option = dms_get_var("slct_srch_doc_owner");
	
	$slct_srch_create_date_option[0] = "";
	$slct_srch_create_date_option[dms_get_var("slct_srch_create_date")] = " SELECTED";
	
	$search_create_date_type = dms_get_var("slct_srch_create_date");
	
	$search_time_stamp = mktime(0,0,0,dms_get_var("slct_srch_create_date_month"),dms_get_var("slct_srch_create_date_day"),dms_get_var("slct_srch_create_date_year"));
	}

// External name search input
if (dms_get_var("search_name") != FALSE)
	{
	$txt_property_name = dms_get_var("search_name");
	$search_type = 2;
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
print "        <input type='hidden' name='hdn_page' value=''>\r";
print "        </form>\r";

// End Of Search Selection Section

if ( ( (dms_get_var("hdn_reload_flag") == "TRUE") || (dms_get_var("search_name") != FALSE) ) && ($page == FALSE) )
	{
	//  If applicable, determine the active folder and it's sub-folders
	$active_folder = dms_active_folder();
	if( ($chk_folder_limit == "CHECKED") && ($active_folder != 0) ) get_folders($active_folder);
	
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

		//switch (dms_get_var("slct_srch_property_name"))
		switch ($search_type)
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
		while($result_data = $dmsdb->getarray($result))
			{
			// Permissions required to view this object:
			//  BROWSE, READONLY, EDIT, OWNER
			$perms_level = dms_perms_level($result_data['obj_id']);
			$perms_level = dms_determine_admin_perms($perms_level);
		
			if ( ($perms_level == 1) || ($perms_level == 2) || ($perms_level == 3) || ($perms_level == 4) )
				{
				$misc_text = $result_data['misc_text'];
				if (strlen($misc_text) >0)
					{
					$misc_text = "&nbsp;&nbsp;&nbsp;(".$misc_text.")";
					}
				else $misc_text = "";
				
				$store_obj_id = $result_data['obj_id'];
				$store_obj_name = $result_data['obj_name'].$misc_text;

				if ($dms_anon_flag == 1)   // If the user is anonymous, provide at most browse or read-only access.
					{    
					switch ($perms_level)
						{
						case 1:
							dms_store_search_results($store_obj_id,$store_obj_name,"D_NAME");
							//print $result_data['obj_name'].$misc_text;
							break;
						case 2:
							dms_store_search_results($store_obj_id,$store_obj_name,"D_OPTIONS");
							break;
						}
					}
				else
					{
					dms_store_search_results($store_obj_id,$store_obj_name,"D_OPTIONS");
					}
				}
			}
		}
	}

print "<tr><td><table>\r";
dms_display_search_results($page);
print "</table></td></tr>\r";

print "</table>\r";
 
include 'inc_pal_footer.php';

?> 
 
 
