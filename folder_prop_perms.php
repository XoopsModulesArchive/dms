<?php
//  ------------------------------------------------------------------------ //
//                     Document Management System                            //
//                  Written By:  Brian E. Reifsnyder                         //
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
// folder_prop_perms.php

include '../../mainfile.php';

include 'inc_perms_check.php';
include_once 'inc_dms_functions.php';


function propagate_perms($folder_id)
	{
	global $dmsdb, $xoopsUser;
 
	// Get the objects in the specified folder.
	$query  = "SELECT * FROM ".$dmsdb->prefix("dms_objects")." ";
	$query .= "WHERE obj_owner='".$folder_id."' ";
	$query .= "ORDER BY obj_type DESC, obj_name";
	
	//  $result = mysql_query($query) or die(mysql_error());
	
	$result = $dmsdb->query($query);
	$num_rows = $dmsdb->getnumrows();
	
	if ($num_rows > 0)
		{
		while($result_data = $dmsdb->getarray($result))
			{
			// If this object is a folder, examine it....otherwise, display the file and move on.
			if($result_data['obj_type'] == FOLDER) 
        			{
				// Change permissions on folder
				set_perms($result_data['obj_id']);
		
				propagate_perms($result_data['obj_id']);
				}
			else
				{
				// Object is a file or link
		
				// Only change permissions is the object is not a link.
				if($result_data['obj_type'] != DOCLINK)
					{
					set_perms($result_data['obj_id']);
					}
				}
			}
		}
	}

function set_perms($folder_id)
	{
	global $perms, $dmsdb;
  
	// Delete all permissions for this object
	$query  = "DELETE FROM ".$dmsdb->prefix("dms_object_perms")." ";
	$query .= "WHERE ptr_obj_id='".$folder_id."'";
	$dmsdb->query($query);
  
	$index = 0;
	while ($perms[$index])
		{
		$query  = "INSERT INTO ".$dmsdb->prefix("dms_object_perms")." ";
		$query .= "(ptr_obj_id,user_id,group_id,user_perms,group_perms,everyone_perms) VALUES ('";
		$query .= $folder_id."','";
		$query .= $perms[$index]["user_id"]."','";
		$query .= $perms[$index]["group_id"]."','";
		$query .= $perms[$index]["user_perms"]."','";
		$query .= $perms[$index]["group_perms"]."','";
		$query .= $perms[$index]["everyone_perms"]."')";
		
		$dmsdb->query($query);
		
		$index ++;
		}
	}
  
  
$index = 0;

//$folder_id = $HTTP_POST_VARS["hdn_obj_id"];  
$folder_id = dms_get_var("hdn_obj_id");

// Get array of permissions for current folder.
$query  = "SELECT * FROM ".$dmsdb->prefix("dms_object_perms")." ";
$query .= "WHERE ptr_obj_id='".$folder_id."' ";
	
$result = $dmsdb->query($query);

while($result_data = $dmsdb->getarray($result))
	{
	$perms [$index]["user_id"]         = $result_data["user_id"];
	$perms [$index]["group_id"]        = $result_data["group_id"];  
	$perms [$index]["user_perms"]      = $result_data["user_perms"];  
	$perms [$index]["group_perms"]     = $result_data["group_perms"];  
	$perms [$index]["everyone_perms"]  = $result_data["everyone_perms"];  
	
	$index++;  
	}      
      
// Propagate permissions to lower sub-dirs.
propagate_perms($folder_id);


//header("Location:folder_options.php?obj_id=".$folder_id);
dms_header_redirect("folder_options.php?obj_id=".$folder_id);
?>
