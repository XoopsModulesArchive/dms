<?php
//  ------------------------------------------------------------------------ //
//                     Document Management System                            //
//                  Written By:  Brian E. Reifsnyder                         //
//                        Copyright 9/26/2008                                //
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

// inc_obj_select.php

include_once '../../mainfile.php';


include_once 'inc_current_version.php';
include_once 'inc_dms_functions.php';

//include_once 'inc_pal_header.php';

//include XOOPS_ROOT_PATH.'/header.php';

//include_once 'inc_dynamic_menus.php';

define('SELECT_FOLDER',1);
define('SELECT_DOCUMENT',2);
$obj_select_num_rad_buttons = 0;

$action = dms_get_var("action");
$browse_obj_id = dms_get_var("browse_obj_id");
if($browse_obj_id == FALSE) $browse_obj_id = dms_get_var("obj_id");

if($action == "exp_folder") // && $browse_obj_id != FALSE)
	{
	dms_set_inbox_status($browse_obj_id);

	// Make sure that this folder, or any other folder, is not marked as active.
	$query = "DELETE FROM ".$dmsdb->prefix("dms_active_folder")." WHERE user_id='".$dms_user_id."'";
	$dmsdb->query($query);

	// Set the folder as active
	$query = "INSERT INTO ".$xoopsDB->prefix("dms_active_folder")." (user_id,folder_id) VALUES ('".$dms_user_id."','".$browse_obj_id."')";
	$dmsdb->query($query);

	dms_get_user_data();
	}

if($action == "ca_folders")
	{
	// Make sure that this folder cannot be marked as active
	$query = "DELETE FROM ".$dmsdb->prefix("dms_active_folder")." WHERE user_id='".$dms_user_id."'";
	$dmsdb->query($query);

	dms_get_user_data();
	}
	
define('SEPARATOR_LIMIT',3);
$separator_counter = 0;
function display_separator()
	{
	global $separator_counter;
	
	$bg_image="images/line.png";
	
	//$separator_counter ++;
	if ($separator_counter > SEPARATOR_LIMIT)
		{
		print "  <tr>\r";
		print "    <td height='1' background='".$bg_image."' nowrap></td>\r";
		//print "    <td background='".$bg_image."' nowrap></td>\r";
		print "    <td background='".$bg_image."' nowrap></td>\r";
		print "    <td background='".$bg_image."' nowrap></td>\r";
		print "  </tr>\r";
		
		$separator_counter = 1;
		}
	}

/*
$new_active_folder = dms_get_var("folder_id");
if($new_active_folder != FALSE) 
	{
	//Ensure that the user has the permission to enter this folder.  If not, break.
	$perms_level = dms_perms_level($new_active_folder);
	if($perms_level >= BROWSE)
		{
		//Make sure that this folder is not marked as expanded in order to prevent multiple entries.
		dms_set_inbox_status($new_active_folder);

		// Make sure that this folder, or any other folder, is not marked as active.
		$query = "DELETE FROM ".$dmsdb->prefix("dms_active_folder")." WHERE user_id='".$dms_user_id."'";
		$dmsdb->query($query);

		// Set the folder as active
		$query = "INSERT INTO ".$xoopsDB->prefix("dms_active_folder")." (user_id,folder_id) VALUES ('".$dms_user_id."','".$new_active_folder."')";
		$dmsdb->query($query);

		$active_folder = $new_active_folder;
		}
	}
*/


/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	


print "<SCRIPT LANGUAGE='Javascript'>\r";
print "  function alpha_sort()\r";
print "    {\r";
print "    var url=\"index.php?ui1_alpha_sort=\" + document.frm_main_ui1.ui1_alpha_sort.value;\r";
print "    location=url;\r";
print "    }\r";
print "</SCRIPT>\r";  

print "<SCRIPT LANGUAGE='Javascript'>\r";
print "  function check_for_dest()\r";
print "    {\r";
print "    var option_selected_flag = 0;\r";
print "    for (var i=0; i < document.frm_select_dest.hdn_num_radio_buttons.value; i++)\r";
print "      {\r";
print "      if(document.frm_select_dest.rad_selected_obj_id[i].checked) option_selected_flag++;\r";
print "      }\r";
print "      if(option_selected_flag > 0)\r";
print "        document.frm_select_dest.submit();\r";
print "      else\r";
print "        alert(\"Please select a destination folder.\");\r";
print "    }\r";
print "</SCRIPT>";

function display_table_header()
	{
	global $active_folder_type,$dms_config;
	
	print "<table width='100%' border='1' cellspacing='0' ".$dms_config['class_content'].">\r";
	print "  <tr>\r";
	


	print "    <td width='2%' ".$dms_config['class_subheader'].">";
	
	switch($active_folder_type)
		{
		case FOLDER:
			print "<img src='images/folder_closed.gif' title='Folder'>";
			break;
		case INBOXEMPTY:
			print "<img src='images/inbox_empty.gif' title='Inbox'>";
			break;
		case INBOXFULL:
			print "<img src='images/inbox_full.gif' title='Inbox'>";
			break;
		default:
		}
	
	print "      </td>\r";

	print "    <td width='5%' ".$dms_config['class_subheader']."></td>\r";
	//print "    <td width='1%' ".$dms_config['class_subheader']."><b></b></td>\r";
	print "    <td align=center width='60%' ".$dms_config['class_subheader']."><b>" . _DMS_ITEM  . "</b></b></td>\r";
	print "  </tr>\r";
	}

function folder_query($obj_owner, $alpha_sort = "ALL")
	{
	global $dms_admin_flag,$dmsdb;

	$query  = "SELECT obj_id,o. ptr_obj_id, obj_type, obj_name, obj_status, obj_owner, obj_checked_out_user_id, lifecycle_id, misc_text, ";
	$query .= "user_id, group_id, user_perms, group_perms, everyone_perms, file_type ";
	$query .= "FROM ".$dmsdb->prefix("dms_objects")." AS o ";
	$query .= "LEFT OUTER JOIN ".$dmsdb->prefix("dms_object_perms")." AS op ";
	$query .= "ON o.obj_id = op.ptr_obj_id ";
	$query .= "WHERE obj_owner = '".$obj_owner."' ";
	if($alpha_sort != "ALL") $query .= "AND obj_name LIKE '".$alpha_sort."%' ";
	if($dms_admin_flag == 0) $query .= "AND obj_status != '".DELETED."' ";
	$query .= "ORDER BY obj_name, obj_id"; 
//print "<BR>".$query."<BR>";
	return $query;
	}

	
// Removes all non-applicable items due to permissions, removes all duplicates

//$sifted_results = array();
function results_sifter($result)
	{
	global $dms_admin_flag,$dms_anon_flag,$dms_config,$dmsdb,$dms_groups,$dms_user_id,$group_list;

	$sifted_results = array();
	$xref_doc_index = array();
	$xref_folder_index = array();
	
	$temp_result_buffer = array();
	
	$temp_result_buffer['obj_id'] = 0;
	$temp_result_buffer['ptr_obj_id'] = 0;
	$temp_result_buffer['obj_type'] = 0;
	$temp_result_buffer['obj_name'] = "";
	$temp_result_buffer['obj_status'] = 0;
	$temp_result_buffer['obj_owner'] = 0;
	$temp_result_buffer['obj_checked_out_user_id'] = 0;
	$temp_result_buffer['lifecycle_id'] = 0;
	$temp_result_buffer['misc_text'] = "";
	$temp_result_buffer['user_id'] = 0;
	$temp_result_buffer['group_id'] = 0;
	$temp_result_buffer['user_perms'] = 0;
	$temp_result_buffer['group_perms'] = 0;
	$temp_result_buffer['everyone_perms'] = 0;
	$temp_result_buffer['file_type'] = 0;
	
	$sifted_results['num_docs'] = 0;
	$sifted_results['num_folders'] = 0;
	
	$sr_index = 0;
	$max_perm = 0;
	
	while($result_data = $dmsdb->getarray($result))
		{
		if( ($temp_result_buffer['obj_id'] > 0) && ($temp_result_buffer['obj_id'] != $result_data['obj_id']) )
			{
			if($max_perm > 0)
				{
				$sifted_results['obj_id'][$sr_index]                  = $temp_result_buffer['obj_id'];
				$sifted_results['ptr_obj_id'][$sr_index]              = $temp_result_buffer['ptr_obj_id'];
				$sifted_results['obj_type'][$sr_index]                = $temp_result_buffer['obj_type'];
				$sifted_results['obj_name'][$sr_index]                = $temp_result_buffer['obj_name'];
				$sifted_results['obj_status'][$sr_index]              = $temp_result_buffer['obj_status'];
				$sifted_results['obj_owner'][$sr_index]               = $temp_result_buffer['obj_owner'];
				$sifted_results['obj_checked_out_user_id'][$sr_index] = $temp_result_buffer['obj_checked_out_user_id'];
				$sifted_results['lifecycle_id'][$sr_index]            = $temp_result_buffer['lifecycle_id'];
				$sifted_results['misc_text'][$sr_index]               = $temp_result_buffer['misc_text'];
				$sifted_results['file_type'][$sr_index]               = $temp_result_buffer['file_type'];
				$sifted_results['max_perm'][$sr_index]                = $max_perm;
				
//				if( ($sifted_results['obj_type'][$sr_index] == FILE) ||
//				    ($sifted_results['obj_type'][$sr_index] == DOCLINK) )   
					
				if( ($sifted_results['obj_type'][$sr_index] == FILE) ||
					($sifted_results['obj_type'][$sr_index] == ROUTEDDOC) ||
					($sifted_results['obj_type'][$sr_index] == WEBPAGE) || 
					($sifted_results['obj_type'][$sr_index] == FILELINK) )
					{
					$xref_doc_index[$sifted_results['num_docs']] = $sr_index;
					$sifted_results['num_docs']++;
					}
				if( ($sifted_results['obj_type'][$sr_index] == FOLDER) ||
					($sifted_results['obj_type'][$sr_index] == FOLDERLINK) )
					{
					$xref_folder_index[$sifted_results['num_folders']] = $sr_index;
					$sifted_results['num_folders']++; 
					}
				
				$sr_index++;
				}
			
			$max_perm = 0;
			$temp_result_buffer['obj_id'] = 0;
			}
		  
		// Store all data in the $temp_result_buffer.  This only changes when the obj_id changes.
		if($temp_result_buffer['obj_id'] == 0)
			{
			$temp_result_buffer['obj_id']                  = $result_data['obj_id'];
			$temp_result_buffer['ptr_obj_id']              = $result_data['ptr_obj_id'];
			$temp_result_buffer['obj_type']                = $result_data['obj_type'];
			$temp_result_buffer['obj_name']                = $result_data['obj_name'];
			$temp_result_buffer['obj_status']              = $result_data['obj_status'];
			$temp_result_buffer['obj_owner']               = $result_data['obj_owner'];
			$temp_result_buffer['obj_checked_out_user_id'] = $result_data['obj_checked_out_user_id'];
			$temp_result_buffer['lifecycle_id']            = $result_data['lifecycle_id'];
			$temp_result_buffer['misc_text']               = $result_data['misc_text'];
			$temp_result_buffer['file_type']               = $result_data['file_type'];
			$temp_result_buffer['user_id']                 = $result_data['user_id'];
			$temp_result_buffer['group_id']                = $result_data['group_id'];
			$temp_result_buffer['user_perms']              = $result_data['user_perms'];
			$temp_result_buffer['group_perms']             = $result_data['group_perms'];
			$temp_result_buffer['everyone_perms']          = $result_data['everyone_perms'];
			}
		
		// Determine the maximum permission for the object.
		if ( ($dms_user_id == $result_data['user_id']) && ($max_perm < $result_data['user_perms']) )
		  $max_perm = $result_data['user_perms'];
//print " u".$max_perm;
 
		$index = 0;
		//while($group_list[$index])
		while($index < $group_list['num_rows'])
			{
			if( ($group_list[$index] == $result_data['group_id']) && ($max_perm < $result_data['group_perms']) )
			  $max_perm = $result_data['group_perms']; 
			$index++;
			}
//print " g".$max_perm;
	 
		if ($result_data['everyone_perms'] > $max_perm) $max_perm = $result_data['everyone_perms'];
//print " e".$max_perm."<BR>";
		
		// If the user is anonymous, grant them a maximum of readonly perms.
		if( ($dms_anon_flag >= 1) && ($max_perm > READONLY) ) $max_perm = READONLY;
//print " a".$max_perm;
	
		// If the user is an administrator and $dms_config['admin_display'] == 1, set the perm level to OWNER
		if( ($dms_admin_flag == 1) && ($dms_config['admin_display'] == '1')) $max_perm = OWNER;
		}
	
	if($max_perm > 0)
		{
		$sifted_results['obj_id'][$sr_index]                  = $temp_result_buffer['obj_id'];
		$sifted_results['ptr_obj_id'][$sr_index]              = $temp_result_buffer['ptr_obj_id'];
		$sifted_results['obj_type'][$sr_index]                = $temp_result_buffer['obj_type'];
		$sifted_results['obj_name'][$sr_index]                = $temp_result_buffer['obj_name'];
		$sifted_results['obj_status'][$sr_index]              = $temp_result_buffer['obj_status'];
		$sifted_results['obj_owner'][$sr_index]               = $temp_result_buffer['obj_owner'];
		$sifted_results['obj_checked_out_user_id'][$sr_index] = $temp_result_buffer['obj_checked_out_user_id'];
		$sifted_results['lifecycle_id'][$sr_index]            = $temp_result_buffer['lifecycle_id'];
		$sifted_results['misc_text'][$sr_index]               = $temp_result_buffer['misc_text'];
		$sifted_results['file_type'][$sr_index]               = $temp_result_buffer['file_type'];
		$sifted_results['max_perm'][$sr_index]                = $max_perm;
		
		if( ($sifted_results['obj_type'][$sr_index] == FILE) ||
			($sifted_results['obj_type'][$sr_index] == ROUTEDDOC) ||
			($sifted_results['obj_type'][$sr_index] == WEBPAGE) ||
   			($sifted_results['obj_type'][$sr_index] == FILELINK) )
			{
			$xref_doc_index[$sifted_results['num_docs']] = $sr_index;
			$sifted_results['num_docs']++;
			}
		if( ($sifted_results['obj_type'][$sr_index] == FOLDER) ||
			($sifted_results['obj_type'][$sr_index] == FOLDERLINK) )
			{
			$xref_folder_index[$sifted_results['num_folders']] = $sr_index;
			$sifted_results['num_folders']++; 
			}
			
		$sr_index++;
		}
	
	$sifted_results['num_rows'] = $sr_index;

//print "<BR>DFT:".$sifted_results['num_docs']." ".$sifted_results['num_folders']." ".$sifted_results['num_rows'];

	$sifted_results['xref_doc'] = $xref_doc_index;
	$sifted_results['xref_folder'] = $xref_folder_index;
	
	return $sifted_results;
	}
	
function page_navigation($sifted_results, $current_row)
	{
	global $dms_config,$dms_var_cache;
	
	$total_docs = $sifted_results['num_docs'];
	$total_folders = $sifted_results['num_folders'];

	$space_exists = FALSE;
	
	// Display alpha sort code
		
	if(  $total_docs > $dms_config['doc_display_limit'] 
	  || $dms_var_cache['doc_alpha_sort'] != "ALL" 
	  || $total_folders > $dms_config['doc_display_limit'])
		{
		print "  <tr><td background='images/line.png' nowrap></td></tr>\r";
		$space_exists = TRUE;
		
		print "  <tr>\r";
		print "    <td align='center'>\r";
		
		if($dms_var_cache['doc_alpha_sort'] == "ALL")
			print "<font color='red'>ALL</font>&nbsp;";
		else
			print "<a href=\"index.php?doc_alpha_sort=-1\">ALL</a>&nbsp;";
		
		for($index = 65;$index <= (65+25);$index++)
			{
			$char_index = chr($index);
			
			if($char_index == $dms_var_cache['doc_alpha_sort'])
				print "<font color='red'>".$char_index."</font>&nbsp;";
			else
				print "<a href=\"index.php?doc_alpha_sort=".$char_index."\">".$char_index."</a>\r";
			}
		print "    </td>\r";
		print "  </tr>\r";
		}

	
	//$sifted_results['xref_doc'] = $xref_doc_index;
	//$sifted_results['xref_folder'] = $xref_folder_index;
	
	$xref_doc_index = $sifted_results['xref_doc'];
	$xref_folder_index = $sifted_results['xref_folder'];
	
	if($total_docs > $dms_config['doc_display_limit']) 
		{
		// NOTE:  All calculated numbers are 0 based.
		
		$border_pages = 15;                  // Set the number of pages displayed on each side of the selected page.
		
		$total_pages = ceil($total_docs/$dms_config['doc_display_limit'])-1;
		$doc_display_start = $dms_var_cache['doc_display_start'];
		$current_page = floor($doc_display_start / $dms_config['doc_display_limit']);
		
		$pn_start_page = $current_page - $border_pages;
		$pn_end_page = $current_page + $border_pages;
		
		// Ensure that n pages, where n = ($border_pages * 2) + 1, are displayed at all times.
		if($pn_start_page < 0) $pn_start_page = 0;
		if( ($pn_end_page - $pn_start_page) < ($border_pages * 2) ) $pn_end_page = $pn_start_page + ($border_pages * 2);
		if($pn_end_page > $total_pages) $pn_end_page = $total_pages;
		if( ($pn_end_page - $pn_start_page) < ($border_pages * 2) ) $pn_start_page = $pn_end_page - ($border_pages * 2);
		if($pn_start_page < 0) $pn_start_page = 0;
		
		$rewind_button_doc_num = ( ($current_page - $border_pages - 1) * $dms_config['doc_display_limit']);
		if($rewind_button_doc_num == 0) $rewind_button_doc_num = -1;
		
		$fast_forward_button_doc_num = ( ($current_page + ($border_pages + 1)) * $dms_config['doc_display_limit']);
		
		// Display the page numbers
		if($space_exists == FALSE) print "  <tr><td background='images/line.png' nowrap></td></tr>\r";
		
		print "  <tr>\r";
		print "    <td align='center'>\r";
		
		if( ($pn_start_page > 0) && ( ($current_page - $border_pages)  > 0) ) 
			print "<a href=\"index.php?doc_display_start=".$rewind_button_doc_num."\"><img src=\"images/sm_arrow_lt.gif\">&nbsp</a>\r";
		
		for($index = $pn_start_page; $index <= $pn_end_page; $index++)
			{
			if($index == $current_page)
				print "<font color='red'>".($index + 1)."</font>&nbsp;";
			else
				{
				$start = $index * $dms_config['doc_display_limit'];
				$title = $sifted_results['obj_name'][$xref_doc_index[$start]];
				
				if($index < $pn_end_page)
				  $title .= "  to  ".$sifted_results['obj_name'][$xref_doc_index[($start + $dms_config['doc_display_limit']-1)]];
				
				if($start == 0) $start = -1;
				print "<a href=\"index.php?doc_display_start=".$start."\" title='".$title."'>".($index + 1)."</a>\r";
				}
			}
		
		if( ($pn_end_page < $total_pages) && ( ($current_page + ($border_pages + 1) ) <= $total_pages ) )
			print "<a href=\"index.php?doc_display_start=".$fast_forward_button_doc_num."\"><img src=\"images/sm_arrow_rt.gif\">&nbsp</a>\r";
		
		print "    </td>\r";
		print "  </tr>\r";
		}
	}
	

function list_folders($folder_owner)
	{
	global $active_folder, $admin_display, $dms_admin_flag; //, $exp_folders;
	global $separator_counter, $xoopsUser;
	global $dms_config, $dms_user_id, $dms_anon_flag;
	global $dmsdb;
	
	global $sifted_results;
	global $obj_select_num_rad_buttons;
	global $obj_id,$browse_obj_id;

	$bg_color="";

	$class = "";
	
	$num_rows = $sifted_results['num_rows'];
	
//print $num_rows;
	if ($num_rows > 0)
		{
		for($folder_index = 0;$folder_index < $num_rows;$folder_index ++)
			{
			// If this object is a folder, examine and possibly display it.
			if  (($sifted_results['obj_type'][$folder_index] == FOLDER) 
			  || ($sifted_results['obj_type'][$folder_index] == FOLDERLINK)
			  || ($sifted_results['obj_type'][$folder_index] == INBOXEMPTY) 
			  || ($sifted_results['obj_type'][$folder_index] == INBOXFULL) 
			  || ($sifted_results['obj_type'][$folder_index] == DISKDIR) )
				{
				$separator_counter++;
				display_separator();
				
				// Determine permissions
				$perm = $sifted_results['max_perm'][$folder_index];
				if($dms_admin_flag == 1)  $perm = OWNER;

				print "  <tr>\r";
				//$index = 0;

print "    <td></td>\r";

				$obj_status = $sifted_results['obj_status'][$folder_index];

				$options_page = "folder_options.php";
				$folder_id = $sifted_results['obj_id'][$folder_index];

				switch ($sifted_results['obj_type'][$folder_index])
					{
					case FOLDER:
						$image = "images/folder_closed.gif";
						$title = _DMS_OPEN_FOLDER;
						if($obj_status == DELETED)
							{
							$image = "images/folder_del_closed.gif";
							$title = _DMS_OPEN_DEL_FOLDER;
							}
						break;
					case DISKDIR:
						$image = "images/folder_closed.gif";
						$title = _DMS_OPEN_FOLDER;
						if($obj_status == DELETED)
							{
							$image = "images/folder_del_closed.gif";
							$title = _DMS_OPEN_DEL_FOLDER;
							}
						break;
					case FOLDERLINK:
						$image = "images/folder_link.gif";
						$title = _DMS_OPEN_FOLDER;
						$options_page = "folder_link_options.php";
						if($obj_status == DELETED)
							{
							$image = "images/folder_link_del.gif";
							$title = _DMS_OPEN_DEL_FOLDER;
							}
						$perm = dms_perms_level($sifted_results['ptr_obj_id'][$folder_index]);
						$folder_id = $sifted_results['ptr_obj_id'][$folder_index];
						break;
					case INBOXEMPTY:
						$image = "images/inbox_empty.gif";
						$title = _DMS_OPEN_INBOX_EMPTY;
						break;
					case INBOXFULL:
						$image = "images/inbox_full.gif";
						$title = _DMS_OPEN_INBOX;
						if($obj_status == DELETED)
						break;
					}

				if($perm > BROWSE)
					{
					print "    <td ".$class." align='left'>";
					print "<input type='radio' name='rad_selected_obj_id' value='".$folder_id."'>";
					print "<a title='".$title."' href='?obj_id=".$obj_id."&browse_obj_id=".$folder_id."&action=exp_folder'><img src='".$image."'></a>";
					$obj_select_num_rad_buttons++;
					}
				else
					print "    <td ".$class." align='left' colspan='2'><img src='".$image."'>";
				
				print "    </td>\r";
				
				if($perm > BROWSE)
					print "    <td align='left'><a href='?obj_id=".$obj_id."&browse_obj_id=".$folder_id."&action=exp_folder'>".$sifted_results['obj_name'][$folder_index]."</a></td>\r";  
				else
					print "    <td align='left'>".$sifted_results['obj_name'][$folder_index]."</td>\r";
					
				print "  </tr>\r";
						
				}
			}
		}
	}

function list_documents($document_owner)
	{
	global $active_folder, $admin_display, $dms_admin_flag, $group_query; //$exp_folders;
	global $separator_counter, $xoopsUser;
	global $dms_config, $dms_user_id, $dms_anon_flag;
	global $dms_var_cache;
	global $dmsdb;
	
	global $sifted_results;
	global $obj_select_num_rad_buttons;

	// If this folder is not active, don't do anthing but exit out of this function.
	if ($document_owner != $active_folder) return(0);
	
	// If this folder is empty, display that it is empty.
	if( ($sifted_results['num_docs'] == 0) && ($sifted_results['num_folders'] == 0) )
		print "  <tr><td colspan='2'></td><td align='center' style='text-align: center;'>Empty</td><td colspan='5'></td></tr>\r";
		
	$bg_color="";
	$bg_image="images/line.gif";

	$num_rows = $sifted_results['num_docs'];
	$disp_start = $dms_var_cache['doc_display_start'];
	if($dms_var_cache['doc_display_start'] > $sifted_results['num_docs']) 
		{
		$disp_start = 0;
		$dms_var_cache['doc_display_start'] = 0;
		}
		
	$disp_end = $num_rows;
	if($num_rows > $dms_config['doc_display_limit']) $disp_end = $disp_start + $dms_config['doc_display_limit'];

	$disp_counter = -1; 
	for($obj_index = 0;$obj_index < $sifted_results['num_rows'];$obj_index ++)
		{
		$disp = FALSE;
		
		if( ($sifted_results['obj_type'][$obj_index] == FILE) ||
		    ($sifted_results['obj_type'][$obj_index] == ROUTEDDOC) ||
		    ($sifted_results['obj_type'][$obj_index] == FILELINK) ||
		    ($sifted_results['obj_type'][$obj_index] == WEBPAGE) )
			{
			$disp_counter++;

			if( ($disp_counter >= $disp_start) && ($disp_counter < $disp_end) )
				{
				$disp = TRUE;
				$separator_counter++;
				display_separator();
				}
			}
		
		if($sifted_results['obj_id'][$obj_index] <=0) $disp=FALSE;
		$obj_id = $sifted_results['obj_id'][$obj_index];
			
		// Determine Permissions
		$perm = $sifted_results['max_perm'][$obj_index];
		
		$class = "";

		$flag_enable_checkin = FALSE;
		$flag_enable_checkout = FALSE;
		$flag_view_doc = FALSE;
		$flag_view_name = FALSE;
		$flag_route_doc = FALSE;
		$flag_disp_options = TRUE;

		$obj_name = $sifted_results['obj_name'][$obj_index];
		$obj_status = $sifted_results['obj_status'][$obj_index];

		//  If the object is a routed document, query the routed document.
		if($sifted_results['obj_type'][$obj_index] == ROUTEDDOC)
			{
			$link_query  = "SELECT obj_id,obj_name,obj_status,current_version_row_id,obj_checked_out_user_id ";
			$link_query .= "from ".$dmsdb->prefix('dms_objects')." ";
			$link_query .= "WHERE obj_id='".$sifted_results['ptr_obj_id'][$obj_index]."'";
			$link_result = $dmsdb->query($link_query,"ROW");

			$perm = dms_perms_level($sifted_results['ptr_obj_id'][$obj_index]);
			$obj_name = $link_result->obj_name;
			$obj_id = $link_result->obj_id;
			$obj_status = $link_result->obj_status;
			}

		//  If the object is a linked document, query the routed document.
		if($sifted_results['obj_type'][$obj_index] == FILELINK)
			{
			$link_query  = "SELECT obj_id,obj_name,obj_status,current_version_row_id,obj_checked_out_user_id ";
			$link_query .= "from ".$dmsdb->prefix('dms_objects')." ";
			$link_query .= "WHERE obj_id='".$sifted_results['ptr_obj_id'][$obj_index]."'";
			$link_result = $dmsdb->query($link_query,"ROW");

			$perm = dms_perms_level($sifted_results['ptr_obj_id'][$obj_index]);
			//$obj_name = $link_result->obj_name;
			$obj_id = $link_result->obj_id;
			$obj_status = $link_result->obj_status;
			}

		if($dms_admin_flag == 1)  $perm = OWNER;


		switch ($perm)
			{
			case OWNER:
			case EDIT:
				$flag_enable_checkout = TRUE;
			case READONLY:
				$flag_view_doc = TRUE;
				$flag_route_doc = TRUE;
			case BROWSE:
				$flag_view_name = TRUE;
				break;
			}

		//  Configure the details to display
		$image = "";
		$options_url = "";
		switch($sifted_results['obj_type'][$obj_index])
			{
			case FILE:
				$image = dms_display_document_icon($sifted_results['obj_id'][$obj_index],
				  $sifted_results['file_type'][$obj_index],
				  $sifted_results['obj_status'][$obj_index]);

				// If there is text to display in misc_text, display it.
				$misc_text = $sifted_results['misc_text'][$obj_index];
				if (strlen($misc_text) >0)
					$misc_text = "&nbsp;&nbsp;&nbsp;(".$misc_text.")";
				else 
					$misc_text = "";
				$title_image = "";
				$title_text = "View Document";
				$options_url = "file_options.php";
				break;
			case ROUTEDDOC:
				// Object is a routed document
				$image = "images/file_routed.gif";
				$title_image = _DMS_DOC_AVAILABLE;
				$title_text = _DMS_VIEW_ROUTED_DOC;
				if($link_result->obj_status == CHECKEDOUT)
					{
					$image = "images/file_routed_locked.gif";
					$title_text = _DMS_DOC_NOT_AVAILABLE;
					}
				$options_url = "link_options.php";
				break;
			case FILELINK:
				$image = "images/doc_types/file_link.png";
				$title_image = "Linked Document";
				$title_text = "View Document";
				$options_url = "file_link_options.php";
				break;
			case WEBPAGE:
				$image = "images/www_open.gif";
				$title_image = "Open Web Page";
				$title_text = "Open Web Page";
				$options_url = "url_options.php";
			}

		//  Determine whether to allow document checkin/checkout.
		$check_in_out_href="file_checkout.php";

		$check_in_out_obj_status = $sifted_results['obj_status'][$obj_index];
		$check_in_out_co_user_id = $sifted_results['obj_checked_out_user_id'][$obj_index];
		$check_in_out_obj_id = $sifted_results['obj_id'][$obj_index];
		if( ($sifted_results['obj_type'][$obj_index] == ROUTEDDOC) )
			{
			$check_in_out_obj_status = $link_result->obj_status;
			$check_in_out_co_user_id = $link_result->obj_checked_out_user_id;
			$check_in_out_obj_id = $link_result->obj_id;
			}

		if($dms_config['checkinout_enable'] == 0)
			{
			$flag_enable_checkout = FALSE;
			$flag_enable_checkin = FALSE;
			}
		else
			{
			if ( ($check_in_out_obj_status == CHECKEDOUT)
			  && ($dms_user_id == $check_in_out_co_user_id)
			  && ($perm >= EDIT) 
			  && ($dms_config['checkinout_enable'] == 1) )
				{
				$flag_enable_checkin = TRUE;
				$check_in_out_href="file_checkin.php";
				}
			}

		if($disp==TRUE)
			{
			print "  <tr>\r";
			print "    <td></td>\r";
			print "    <td ".$dms_config['class_content']." align='left' valign='top'>";
			print "<input type='radio' name='rad_selected_obj_id' value='".$obj_id."'>";
			print "<a title='".$title_image."'><img src='".$image."'></a></td>\r";

			print "    <td align='left'>";
			print $obj_name.$misc_text;
			print "    </td>\r";
			print "  </tr>\r";
			$obj_select_num_rad_buttons++;
			}
		}
	}

// Top of page
function dms_select_object_id($function, $current_obj_id)
	{
	global $active_folder, $admin_display, $dms_admin_flag, $group_query; //$exp_folders;
	global $separator_counter, $xoopsUser;
	global $dms_config, $dms_user_id, $dms_anon_flag;
	global $dms_var_cache;
	global $dmsdb;
	
	global $sifted_results;
	global $obj_select_num_rad_buttons;

	global $dms_groups, $active_folder_perms;

	//print "<form name='frm_main_ui1'>\r";
		
	// Determine which page of documents is to be displayed and whether the list should be limited alphabetically.
	if(!isset($dms_var_cache['doc_display_start']))  
		{
		$dms_var_cache['doc_display_start'] = 0;
		$dms_var_cache['doc_alpha_sort'] = "ALL";
		}

	$temp_var = dms_get_var("doc_display_start");
	if($temp_var != FALSE) $dms_var_cache['doc_display_start'] = $temp_var;
	if($dms_var_cache['doc_display_start'] == -1) $dms_var_cache['doc_display_start'] = 0;

	$temp_var = dms_get_var("doc_alpha_sort");
	if($temp_var != FALSE) $dms_var_cache['doc_alpha_sort'] = $temp_var;
	if($dms_var_cache['doc_alpha_sort'] == -1) $dms_var_cache['doc_alpha_sort'] = "ALL";

	dms_var_cache_save();

	// get list of groups that this user is a member of 
	$group_list = $dms_groups->grp_list();

	// If the $active_folder_perms <= BROWSE go back to index.php.
	if( ($active_folder_perms) <= BROWSE && ($active_folder!=0) )
		{
		dms_redirect("index.php");
		exit(0);
		}
	
	//  Display current location in DMS by displaying only the branches on the tree leading to this open folder
	$loc_obj_owner = $active_folder;
	$loc_loop_flag = TRUE;
	$loc_index = 0;

	print "<table width='100%' border = '1' cellspacing = '0' ".$dms_config['class_content'].">\r";
	print "  <tr>\r";
	print "    <td style='text-align: left;'>\r";

	$loc_total_string_length = 0;
	while($loc_loop_flag == TRUE)
		{
		$query  = "SELECT obj_owner,obj_name FROM ".$dmsdb->prefix("dms_objects")." WHERE obj_id='".$loc_obj_owner."'";
		$result = $dmsdb->query($query,"ROW");
		if($dmsdb->getnumrows() == 0) break;
		
		$loc_obj_name[$loc_index] = $result->obj_name;
		$loc_obj_id[$loc_index] = $loc_obj_owner;
		
		$loc_total_string_length += strlen($loc_obj_name[$loc_index]) + 8;
		
		$loc_obj_owner = $result->obj_owner;
		
		if($result->obj_owner == 0) $loc_loop_flag = FALSE;
		
		$loc_index++;
		}

	$loc_obj_name[$loc_index] = "Top";
	$loc_obj_id[$loc_index] = 0;
		
		
	$loc_between_flag = FALSE;
	$loc_max_string_length = 120;           // Set the maximum line length to 120
	
	$indent = 0;
	for($index = $loc_index; $index >= 0; $index--)
		{
		$folder_change_action = "exp_folder";
		if($index == $loc_index) $folder_change_action = "ca_folders";
	
		if($loc_total_string_length > $loc_max_string_length)             // Multiple Line Folder Display
			{
			if($loc_between_flag == TRUE) print "<BR>\r"; 
			
			dms_display_spaces($indent * 3);                     // Indent 3 spaces at each level
			$indent += 1;
			}
		else                                                         // Single Line Folder Display
			{
			if($loc_between_flag == TRUE) print "&nbsp;&nbsp;&nbsp;&gt;&gt;&nbsp;&nbsp;&nbsp;";
			}
		
		print "<a href='?obj_id=".$current_obj_id."&action=".$folder_change_action."&browse_obj_id=".$loc_obj_id[$index]."'>".$loc_obj_name[$index]."</a>";
		
		if($loc_between_flag == FALSE) $loc_between_flag = TRUE;
		}
		
		
	print "\r    </td>\r";
	print "  </tr>\r";
	print "</table>\r";
	
	$query  = "SELECT obj_type FROM ".$dmsdb->prefix("dms_objects")." ";
	$query .= "WHERE (obj_id = '".$active_folder."')";
	$folder_owner_type = $dmsdb->query($query,"obj_type");
	
	// List only the active folder
	$level = 1;
	
	$query = folder_query($active_folder,$dms_var_cache['doc_alpha_sort']);
	$result = $dmsdb->query($query);
	$sifted_results = results_sifter($result);

	page_navigation($sifted_results,$disp_start);

	display_table_header();

	list_folders($active_folder);
	if($function == SELECT_DOCUMENT)
		list_documents($active_folder);

	$disp_start = $dms_var_cache['doc_display_start'];
	if($dms_var_cache['doc_display_start'] > $sifted_results['num_docs']) 
		{
		$disp_start = 0;
		$dms_var_cache['doc_display_start'] = 0;
		}
	
	print "</table>\r";
	
	print "<input type='hidden' name='hdn_num_radio_buttons' value='".$obj_select_num_rad_buttons."'>\r";
	//print "</form>\r";
}

?>
