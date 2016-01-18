<?php
//  ------------------------------------------------------------------------ //
//                     Document Management System                            //
//                  Written By:  Brian E. Reifsnyder                         //
//                        Copyright 5/13/2003                                //
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

// Main Menu
// inc_file_select.php


//include_once 'defines.php';
//include_once 'inc_dms_functions.php';


$level = 0;
function list_folder($folder_owner)
	{
	global $dms_admin_flag, $admin_display, $active_folder, $exp_folders, $group_query, $level, $location, $xoopsUser;
	global $dms_config,$module_url;
	global $dmsdb, $dms_user_id, $dms_tab_index;
	
	$bg_color="";
	$user_id = $dms_user_id;
    
	// Set up display offsets
	$index=0;
	$level_offset = "";
	while($index < $level)
		{
		$level_offset .= "&nbsp;&nbsp;&nbsp;";	
		$index++;
		}
  
	// If the user is an administrator, ignore the permissions entirely.
	if ( ($dms_admin_flag == 1) && ($admin_display == '1') )
		{
		$query  = "SELECT * FROM ".$dmsdb->prefix("dms_objects")." ";
		$query .= "WHERE (obj_owner='".$folder_owner."') AND (obj_status < 2)";
		$query .= "ORDER BY obj_type DESC, obj_name";
		}
  	else
		{
		$query  = "SELECT obj_id, ".$dmsdb->prefix("dms_objects").".ptr_obj_id, obj_type, obj_name, ";
		$query .= "obj_status, obj_owner, obj_checked_out_user_id, lifecycle_id, ";
		$query .= "user_id, group_id, user_perms, group_perms, everyone_perms ";
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

		//print $query;
		//exit(0);
		}
   
  
	$result = $dmsdb->query($query);
	$num_rows = $dmsdb->getnumrows();
  
	if ($num_rows > 0)
		{
		while($result_data = $dmsdb->getarray($result))
			{
			if($dms_admin_flag == 1)  $perm = OWNER;
			else                      $perm = dms_perms_level($result_data['obj_id']);
	  
			// Set class to nothing.
			$class = "";
			
			// If this object is a folder, examine it....otherwise, display the file and move on.
			if($result_data['obj_type'] == FOLDER)
				{
				$index = 0;
				$exp_flag = 0;
		
				// Is folder expanded?
				while($exp_folders[$index] != -1)
					{ 
					if ($exp_folders[$index] == $result_data['obj_id']) $exp_flag = 1;
					$index++;
					}
		
				if (($exp_flag==1) && ($perm > BROWSE))
					{
					print "  <tr>\r";
					if($result_data['obj_status'] == DELETED)
						{
						print "    <td align='left'>".$level_offset."<a href='".$module_url."folder_contract.php?ret_location=".$location."&folder_id=".$result_data['obj_id']."&obj_id=-1&active=FALSE'><img src='".$module_url."images/folder_del_open.png'></a>&nbsp;&nbsp;&nbsp;\r";
						}
					else
						{
						print "    <td align='left'>".$level_offset."<a href='".$module_url."folder_contract.php?ret_location=".$location."&folder_id=".$result_data['obj_id']."&obj_id=-1&active=FALSE'><img src='".$module_url."images/folder_open.png'></a>&nbsp;&nbsp;&nbsp;\r";
						}
					}
				else
					{
					print "  <tr>\r";
					if ($perm > BROWSE)
						{
						if ($result_data['obj_status'] == DELETED) 
							{
							print "    <td align='left'>".$level_offset."<a href='".$module_url."folder_expand.php?ret_location=".$location."&folder_id=".$result_data['obj_id']."&obj_id=-1&active=FALSE'><img src='".$module_url."images/folder_del_closed.png'></a>&nbsp;&nbsp;&nbsp;\r";
							}
						else
							{
							print "    <td align='left'>".$level_offset."<a href='".$module_url."folder_expand.php?ret_location=".$location."&folder_id=".$result_data['obj_id']."&obj_id=-1&active=FALSE'><img src='".$module_url."images/folder_closed.png'></a>&nbsp;&nbsp;&nbsp;\r";
							}
						}
					else
						{
						if ($result_data['obj_status'] == DELETED)
							{
							print "    <td align='left'>".$level_offset."<img src='".$module_url."images/folder_del_closed.png'></a>&nbsp;&nbsp;&nbsp;\r";
							}
						else
							{
							print "    <td align='left'>".$level_offset."<img src='".$module_url."images/folder_closed.png'></a>&nbsp;&nbsp;&nbsp;\r";
							}
						}
					}
		   
				print "    ".$result_data['obj_name']."</td>\r";
				print "    <td></td>\r  </tr>\r";
		
				if (($exp_flag == 1) && ($perm > BROWSE))
					{
					$level++;
					list_folder($result_data['obj_id']);
					$level--;
					}
				}
			else
				{
				// Object is a file or link
		
				// Check if the object is a file or link
				if( ($result_data['obj_type'] == FILE) && ($perm > BROWSE) )
					{
					// Object is a file
					print "  <tr>\r";
					print "    <td align='left'>".$level_offset."<input type='radio' name='rad_file_id' value='".$result_data['obj_id']."' tabindex='".$dms_tab_index++."'>";
					print "<img src='".$module_url."images/file_text.png'>&nbsp;&nbsp;&nbsp;\r";
				  
					print $result_data['obj_name']."</td>\r";
					
					printf("    <td></td>\r  </tr>\r");
					}
				}
			}
		}
	else
		{
		print "  <tr><td>".$level_offset."Empty</td><td colspan='5'></td></tr>\r";
		}
	}

// If the user is an Admin, get the admin_display value
if ($dms_admin_flag == 1)
	{
	$admin_display = $dms_config['admin_display'];
	}
else 
	{
	$admin_display = '0';
	}
   
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
$query = "SELECT folder_id FROM ".$dmsdb->prefix("dms_exp_folders")." WHERE user_id='".$dms_user_id."'";
$result = $dmsdb->query($query);

$index = 0;
while($result_data = $dmsdb->getarray($result) )
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

print "<table>\r";
list_folder($root_folder);
print "</table>\r";


?>
