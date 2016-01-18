<?php
//  ------------------------------------------------------------------------ //
//                     Document Management System                            //
//                  Written By:  Brian E. Reifsnyder                         //
//                        Copyright 4/27/2005                                //
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

// DMS Functions
// inc_lifecycle_functions.php

function dms_apply_lifecycle($obj_id,$lifecycle_id)
{
	global $dmsdb,$dms_config;

	// Ensure that the lifecycle has stages.  If there aren't any stages found, return an error.
	$query  = "SELECT count(*) as number_of_rows  ";
	$query .= "FROM ".$dmsdb->prefix('dms_lifecycle_stages')." ";
	$query .= "WHERE lifecycle_id='".$lifecycle_id."' ";
	$query .= "ORDER BY lifecycle_stage";
	$number_of_rows = $dmsdb->query($query,'number_of_rows');
	
	if($number_of_rows == 0) 
		{
		include XOOPS_ROOT_PATH.'/header.php';
		print "<BR>\r";
		print _DMS_LIFECYCLE_COFIG_ERROR . "\r";
		include_once XOOPS_ROOT_PATH.'/footer.php';
		exit(0);
		}
	
	// Get the destination information for the first stage of the lifecycle
	$query  = "SELECT lifecycle_id, lifecycle_stage, new_obj_location,flags,opt_obj_copy_location ";
	$query .= "FROM ".$dmsdb->prefix('dms_lifecycle_stages')." ";
	$query .= "WHERE lifecycle_id='".$lifecycle_id."' ";
	$query .= "ORDER BY lifecycle_stage LIMIT 1";
	$result = $dmsdb->query($query,'ROW');
	
	// If the foo_copy_flag is set, leave a copy of the document in the active folder.
	// (In reality, a copy of the document is created, then the lifecycle is applied to the copy.)
	if( ($result->flags & 2) == 2)
		{
		$active_folder = dms_active_folder();
		
		$source_obj_id=$obj_id;
		
		$obj_id = dms_file_copy($obj_id,$active_folder);
		dms_auditing($obj_id,"document/lc_copy/source obj id=".$source_obj_id);
		}
	
	// Move the file to the folder for the first stage and add the lifecycle id and stage.
	$query  = "UPDATE ".$dmsdb->prefix('dms_objects')." ";
	$query .= "SET ";
	$query .= "obj_owner='".$result->new_obj_location."', ";
	$query .= "lifecycle_id='".$result->lifecycle_id."', ";
	$query .= "lifecycle_stage='".$result->lifecycle_stage."' ";
	$query .= "WHERE obj_id='".$obj_id."'";
	$dmsdb->query($query);

	// Update the misc_text in the dms_objects table
	dms_update_misc_text($obj_id);
	
	// Apply the new lifecycle permissions to the document.
	dms_set_lifecycle_stage_perms($obj_id,$result->lifecycle_id,$result->lifecycle_stage);
	
	dms_alpha_move($obj_id);
	
	// If the lifecycle only has one stage, remove it from the lifecycle system.
	if($number_of_rows == 1)
		{
		if($dms_config['lifecycle_name_preserve'] == 0) dms_update_misc_text($obj_id);
		
		// Remove the document from the lifecycle
		$query  = "UPDATE ".$dmsdb->prefix('dms_objects')." ";
		$query .= "SET ";
		$query .= "lifecycle_id='0', ";
		$query .= "lifecycle_stage='0' ";
		$query .= "WHERE obj_id='".$obj_id."'";
		$dmsdb->query($query);
		}
	
	dms_document_name_sync($obj_id);
		
	// If there is a location to copy the document to, copy it and set it's permissions.
	if($result->opt_obj_copy_location > 0) 
		{
		$copy_obj_id = dms_file_copy($obj_id,$result->opt_obj_copy_location);
		dms_set_lifecycle_stage_perms($copy_obj_id,$result->lifecycle_id,$result->lifecycle_stage);
		
		dms_auditing($obj_id,"document/lc_copy/dest obj id=".$copy_obj_id."/dest folder id=".$result->opt_obj_copy_location);
		dms_auditing($copy_obj_id,"document/lc_copy/source obj id=".$obj_id);
		}
		
	return $result->new_obj_location;
}

function dms_set_lifecycle_stage_perms($obj_id,$lifecycle_id,$lifecycle_stage)
	{
	global $dmsdb;
	
	// Get the obj_id and change_perms_flag for the stage of the lifecycle
	$query  = "SELECT obj_id, flags,perms_group_id ";
	$query .= "FROM ".$dmsdb->prefix('dms_lifecycle_stages')." WHERE ";
	$query .= "lifecycle_id='".$lifecycle_id."' AND ";
	$query .= "lifecycle_stage='".$lifecycle_stage."'";
//print "<BR>".$query;
	$result = $dmsdb->query($query,'ROW');
	$ls_obj_id = $result->obj_id;

	// If the change_perms_flag (now flags) is not set to 1, exit this function
	if ( ($result->flags & 1) != 1 ) return(0);
	
	// If a permissions group is set, change the object to the permissions set in the permissions group, then exit.
	if ($result->perms_group_id > 0)
		{
		dms_perms_apply_group($result->perms_group_id,$obj_id);
		return(0);
		}
	
	// Delete the permissions for the object.
	$query  = "DELETE FROM ".$dmsdb->prefix("dms_object_perms")." ";
	$query .= "WHERE ptr_obj_id='".$obj_id."'";
	$dmsdb->query($query);
	
	// Copy the lifecycle stage permissions to the object.
	$query  = "SELECT * FROM ".$dmsdb->prefix("dms_object_perms")." ";
	$query .= "WHERE ptr_obj_id='".$ls_obj_id."'";
	$result = $dmsdb->query($query);
	
	while($result_data = $dmsdb->getarray($result))
		{
		$query  = "INSERT INTO ".$dmsdb->prefix("dms_object_perms")." ";
		$query .= "(ptr_obj_id,user_id,group_id,user_perms,group_perms,everyone_perms) VALUES (";
		$query .= "'".$obj_id."',";
		$query .= "'".$result_data['user_id']."',";
		$query .= "'".$result_data['group_id']."',";
		$query .= "'".$result_data['user_perms']."',";
		$query .= "'".$result_data['group_perms']."',";
		$query .= "'".$result_data['everyone_perms']."')";
		$dmsdb->query($query);
		}
	}


