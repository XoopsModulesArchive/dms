<?php
//  ------------------------------------------------------------------------ //
//                     Document Management System                            //
//                  Written By:  Brian E. Reifsnyder                         //
//                        Copyright 2003                                     //
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

// inc_folder_select.php
//include_once 'inc_perms_check.php';
//include_once 'defines.php';

print "<SCRIPT LANGUAGE='Javascript'>\r";
print "  function check_for_dest()\r";
print "    {\r";
print "    var option_selected_flag = 0;\r";
print "    for (var i=0; i < document.frm_select_dest.hdn_num_radio_buttons.value; i++)\r";
print "      {\r";
print "      if(document.frm_select_dest.rad_folder_id[i].checked) option_selected_flag++;\r";
print "      }\r";
print "      if(option_selected_flag > 0)\r";
print "        document.frm_select_dest.submit();\r";
print "      else\r";
print "        alert(\"Please select a destination folder.\");\r";
print "    }\r";
print "</SCRIPT>";  

$level = 0;
function dms_folder_list_folder($folder_owner)
	{
	global $admin_display, $admin_flag, $active_folder, $exp_folders, $obj_id, $group_query, $level;
	global $location;
	global $module_url;
	global $dmsdb, $dms_user_id;
	global $num_radio_buttons;
		
	$bg_color="";
	$user_id = $dms_user_id;
    
	// Set up display offsets
	$level_offset = "";
	$index=0;
	while($index < $level)
		{
		$level_offset .= "&nbsp;&nbsp;&nbsp;";	
		$index++;
		}
  
	// If the user is an administrator, ignore the permissions entirely.
	if ( ($admin_flag == 1) && ($admin_display=='1') )
		{
		$query  = "SELECT * FROM ".$dmsdb->prefix("dms_objects")." ";
		$query .= "WHERE (obj_owner='".$folder_owner."') AND (obj_status < 2)";
		$query .= "ORDER BY obj_type DESC, obj_name";
		}
	else
		{
		$query  = "SELECT obj_id, ".$dmsdb->prefix("dms_objects").".ptr_obj_id, obj_type, obj_name, obj_status, obj_owner, ";
		$query .= "obj_checked_out_user_id,user_id, group_id, user_perms, group_perms, everyone_perms ";
		$query .= "FROM ".$dmsdb->prefix("dms_object_perms")." ";
		$query .= "INNER JOIN ".$dmsdb->prefix("dms_objects")." ON ";
		$query .= $dmsdb->prefix("dms_object_perms").".ptr_obj_id = obj_id ";
		$query .= "WHERE (obj_owner='".$folder_owner."') ";
		$query .= " AND (";
		$query .= "    everyone_perms !='0'";
		$query .= $group_query;
		$query .= " OR user_id='".$user_id."'";
		$query .= ")";
		$query .= " AND (obj_status < 2) ";
		$query .= "GROUP BY obj_id ";
		$query .= "ORDER BY obj_type DESC, obj_name";
		}
	
//print $query;
//exit(0);
	$result = $dmsdb->query($query);
	$num_rows = $dmsdb->getnumrows($result);
  
	if ($num_rows > 0)
		{
		while($result_data = $dmsdb->getarray($result))
			{
			if($admin_flag == 1)  $perm = OWNER;
			else                  $perm = dms_perms_level($result_data['obj_id']);
	  
			// Set class to the active background color
			$class = "";
	    
			// If this object is a folder, display it.
			if($result_data['obj_type'] == FOLDER || $result_data['obj_type']==DISKDIR)
				{
				print "  <tr>\r";
		
				$index = 0;
				$exp_flag = 0;

				// Is folder expanded?
				while($exp_folders[$index] != -1)
					{ 
					if ($exp_folders[$index] == $result_data['obj_id']) $exp_flag = 1;
					$index++;
					}
		
				// Display standard folders
				if ($result_data['obj_type']==FOLDER || $result_data['obj_type']==DISKDIR) 
					{
					$num_radio_buttons++;
					
					if (($exp_flag==1) && ($perm > BROWSE))
						{
						print "    <td align='left' nowrap ".$class.">";
						print "<input type='radio' name='rad_folder_id' value='".$result_data['obj_id']."'>";
						print $level_offset;
						print "<a href='".$module_url."folder_contract.php?ret_location=".$location."&folder_id=".$result_data['obj_id']."&obj_id=".$obj_id."&active=false'>";
						print "<img src='".$module_url."images/folder_open.png'></a>&nbsp;&nbsp;&nbsp;\r";
						}
					else
						{
						if ($perm > BROWSE)
							{
							print "    <td align='left' nowrap ".$class.">";
							print "<input type='radio' name='rad_folder_id' value='".$result_data['obj_id']."'>";
							print $level_offset;
							print "<a href='".$module_url."folder_expand.php?ret_location=".$location."&folder_id=".$result_data['obj_id']."&obj_id=".$obj_id."&active=false'>";
							print "<img src='".$module_url."images/folder_closed.png'></a>&nbsp;&nbsp;&nbsp;\r";
							}
						/*
						else
							{
							print "    <td align='left' nowrap ".$class.">";
							print "<input type='radio' name='rad_folder_id' value='".$result_data['obj_id']."'>";
							print $level_offset;
							print "<img src='".$module_url."images/folder_closed.png'></a>&nbsp;&nbsp;&nbsp;\r";
							}
						*/
						}
					} 
 
				// If folder is not active, display the name and link to make it active, otherwise just display the name.
				if (($result_data['obj_id'] == $active_folder) || ($perm == BROWSE))
					{
					print "    ".$result_data['obj_name']."</td>\r";
					}   
				else
					{
					print "    <a href='".$module_url."folder_expand.php?ret_location=".$location."&folder_id=".$result_data['obj_id']."&obj_id=".$obj_id."&active=false'>";
					print $result_data['obj_name']."</a></td>\r";  
					}
		
				print "    </td>\r";
				//print "    <td width='100%'></td>\r  </tr>\r";
		
				if (($exp_flag == 1) && ($perm > BROWSE))
					{
					$level++;
					dms_folder_list_folder($result_data['obj_id']);
					$level--;
					}
				}
			}
		}
	}

	
// If the $folder_select_post_data variable is set and is TRUE, then instead of each folder change using a querystring, the javascript function
// specified in $folder_select_javascript will be executed.
//if(!isset($folder_select_post_data)) $folder_select_post_data=FALSE;
//else $folder_select_post_data=TRUE;
	
// Determine if the user is an administrator
if($xoopsUser->IsAdmin()) 
	{
	$admin_flag = 1;
	}
else 
	{
	$admin_flag = 0;
	}

// If the user is an Admin, get the admin_display value
if ($admin_flag == 1)
	{
	$query  = "SELECT data FROM ".$dmsdb->prefix('dms_config')." ";
	$query .= "WHERE name='admin_display'";
	$admin_display = $dmsdb->query($query,'data');
	}
else 
	{
	$admin_display = '0';
	}


// Counter of the number of radio buttons
$num_radio_buttons = 0;
	   
// get list of groups that this user is a member of and create part of the query
// also, place these groups into an array for later use
$group_list = $xoopsUser->getGroups();
$group_array = array();
$index = 0;

$group_query = "";
do  
	{
	$group_query .= " OR group_id='".current($group_list)."'";
	$group_array[$index] = current($group_list);
  
	$index++;
	} while(next($group_list));
  
// Get list of expanded folders
$query = sprintf("SELECT * FROM %s WHERE user_id='%s'",$dmsdb->prefix("dms_exp_folders"),$dms_user_id);
$result = $dmsdb->query($query);

$index = 0;
while($result_data = $dmsdb->getarray($result))
	{
	$exp_folders[$index]=$result_data['folder_id'];  
	$index++;
	} 
$exp_folders[$index]=-1;

// Get active folder
$query = "SELECT folder_id from ".$dmsdb->prefix("dms_active_folder")." WHERE user_id='".$dms_user_id."'";  
$active_folder = $dmsdb->query($query,'folder_id');
if(!$active_folder>=1) $active_folder=0;

// Determine module url
$module_url = XOOPS_URL."/modules/dms/";
          
// List all folders
print "<table>\r";
dms_folder_list_folder(0);
print "</table>\r";

print "<input type='hidden' name='hdn_num_radio_buttons' value='".$num_radio_buttons."'>\r";

?>
