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

// lifecycle_promote.php

include '../../mainfile.php';
include_once 'inc_dms_functions.php';
include_once 'inc_lifecycle_functions.php';
include_once 'inc_file_copy.php';
include_once 'inc_dest_path_and_file.php';

// Determine which web page to return to.
$return_url = "";
if ($HTTP_GET_VARS["return_url"])      $return_url = $HTTP_GET_VARS["return_url"];
if ($HTTP_POST_VARS["hdn_return_url"]) $return_url = $HTTP_POST_VARS["hdn_return_url"];
if (strlen($return_url) <= 1)          $return_url = "index.php"; 

if($HTTP_POST_VARS["hdn_function"]) 
	{
	$function = $HTTP_POST_VARS["hdn_function"];
	$file_id = $HTTP_POST_VARS["hdn_file_id"];
	}
else 
	{
	$file_id = $HTTP_GET_VARS["obj_id"];
	}

if ($function == "PROMOTE")
	{
	// get current lifecycle info
	$query  = "SELECT current_version_row_id, obj_owner, lifecycle_id, lifecycle_stage ";
	$query .= "FROM ".$dmsdb->prefix('dms_objects')." ";
	$query .= "WHERE obj_id='".$file_id."' ";
	$result = $dmsdb->query($query,'ROW');
	
	$current_lifecycle_id = $result->lifecycle_id;
	$current_lifecycle_stage = $result->lifecycle_stage;
	$current_object_location = $result->obj_owner;
	$current_version_row_id = $result->current_version_row_id;
	
	$old_lifecycle_id = $current_lifecycle_id;
	$old_lifecycle_stage = $current_lifecycle_stage;

/*
	$query  = "SELECT opt_obj_copy_location FROM ".$dmsdb->prefix('dms_lifecycle_stages')." ";
	$query .= "WHERE lifecycle_id='".$old_lifecycle_id."' AND lifecycle_stage = '".$old_lifecycle_stage."'";
print $query;
	$old_opt_obj_copy_location = $dmsdb->query($query,"opt_obj_copy_location");
print "ooocl:  ".$old_opt_obj_copy_location;
*/

	// query all lifecycle stages for this lifecycle
	$query  = "SELECT lifecycle_stage, new_obj_location, flags, opt_obj_copy_location ";
	$query .= "FROM ".$dmsdb->prefix('dms_lifecycle_stages')." ";
	$query .= "WHERE lifecycle_id='".$current_lifecycle_id."' ";
	$query .= "ORDER BY lifecycle_stage";
	$result = $dmsdb->query($query);
	
	
	$last_lifecycle_stage = 0;
	$lifecycle_flag = "SEARCHING";
	
	$new_lifecycle_stage = 0;
	
	$final_lifecycle_stage_flag = FALSE;
	
	// find next lifecycle
	while($result_data = $dmsdb->getarray($result))
		{  
		if ($lifecycle_flag == "FOUND") 
			{
			$new_lifecycle_stage = $result_data['lifecycle_stage'];  
			$new_obj_location = $result_data['new_obj_location'];
			$flags = $result_data['flags'];
			$opt_obj_copy_location = $result_data['opt_obj_copy_location'];
			$lifecycle_flag = "CHANGE";
			}
		
		if ($result_data['lifecycle_stage'] == $current_lifecycle_stage) $lifecycle_flag = "FOUND"; 
	
		$last_lifecycle_stage = $result_data['lifecycle_stage'];
		}
	
		// Store the information for changing permissions.
		$perms_current_lifecycle_id = $current_lifecycle_id;
		$perms_new_lifecycle_stage = $new_lifecycle_stage;
		
	// if there are no more lifecycles, remove document from lifecycle system
	if ( ($lifecycle_flag != "CHANGE") || ($last_lifecycle_stage == $new_lifecycle_stage) )
		{
		if($dms_config['lifecycle_name_preserve'] == 1) dms_update_misc_text($file_id,$new_lifecycle_stage);
		
		$current_lifecycle_id = 0;
		$new_lifecycle_stage = 0;
		if($lifecycle_flag != "CHANGE") $new_obj_location = $current_object_location;
		
		$final_lifecycle_stage_flag = TRUE;
		
		// if only the last revision of the document is to be kept, delete all previous revisions
		if($dms_config['lifecycle_del_previous'] == 1)
			{
			$query  = "SELECT row_id, file_path FROM ".$dmsdb->prefix("dms_object_versions")." ";
			$query .= "WHERE obj_id='".$file_id."'";
			$result = $dmsdb->query($query);
			
			while($result_data=$dmsdb->getarray($result))
				{
				// only delete documents that are not the current version.
				if($result_data['row_id'] != $current_version_row_id)
					{
					$file_path = $dms_config['doc_path']."/".$result_data['file_path'];
					unlink($file_path);
					
					$query  = "DELETE FROM ".$dmsdb->prefix("dms_object_versions")." ";
					$query .= "WHERE row_id='".$result_data['row_id']."'";
					$dmsdb->query($query);
					}
				}
			}
		}
	
		
	// If the foo_copy_flag is set, leave a copy of the document in the source folder.
	if( ($flags & 2) == 2)  
		{
		$copy_obj_id = dms_file_copy($file_id,$current_object_location);
		dms_set_lifecycle_stage_perms($copy_obj_id,$old_lifecycle_id,$old_lifecycle_stage);
		}
		
	// Update the file properties...move the document to the approprate folder.
	$query  = "UPDATE ".$dmsdb->prefix('dms_objects')." ";
	$query .= "SET ";
	$query .= "obj_owner='".$new_obj_location."', ";
	$query .= "lifecycle_id='".$current_lifecycle_id."', ";
	$query .= "lifecycle_stage='".$new_lifecycle_stage."' ";
	$query .= "WHERE obj_id='".$file_id."'";
	$dmsdb->query($query);
	
	if($final_lifecycle_stage_flag == FALSE) dms_update_misc_text($file_id);
	if( ($dms_config['lifecycle_name_preserve'] == 0) && ($final_lifecycle_stage_flag == TRUE) ) dms_update_misc_text($file_id);
	
	dms_set_lifecycle_stage_perms($file_id,$perms_current_lifecycle_id,$perms_new_lifecycle_stage);

	// If there is a location to copy the document to, copy it and set it's permissions.
	if($opt_obj_copy_location > 0) 
		{
		$copy_obj_id = dms_file_copy($file_id,$opt_obj_copy_location);
		dms_set_lifecycle_stage_perms($copy_obj_id,$perms_current_lifecycle_id,$perms_new_lifecycle_stage);
		}
	
	dms_alpha_move($file_id);
	
	dms_auditing($file_id,"document/lifecycle/promote id=".$current_lifecycle_id.",dest folder=".$new_obj_location);
	
	dms_message("The document has been promoted to the next stage in the lifecycle.");

	// Return to the options screen
	//header("Location:file_options.php?obj_id=".$file_id);
	dms_header_redirect("file_options.php?obj_id=".$file_id);
}
else
{
	include XOOPS_ROOT_PATH.'/header.php';
	
	// Get file information
	$query  = "SELECT obj_name from ".$dmsdb->prefix("dms_objects")." ";
	$query .= "WHERE obj_id='".$file_id."'";  
	$doc_name = $dmsdb->query($query,'obj_name');
	
	print "<form method='post' action='lifecycle_promote.php'>\r";
	print "<table width='100%'>\r";
	
	display_dms_header();
	
	print "  <tr><td colspan='2'><BR></td></tr>\r";
	print "  <tr><td colspan='2' align='left'><b>Promote Document:</b></td></tr>\r";
	print "  <tr><td colspan='2'><BR></td></tr>\r";
	print "  <tr>\r";
	print "    <td colspan='2' align='left'>File Name:&nbsp;&nbsp;&nbsp;";
	print "        ".$doc_name."</td>\r";
	print "  </tr>\r";
	print "  <tr><td colspan='2'><BR></td></tr>\r";
	print "  <td colspan='2' align='left'><input type=submit name='btn_submit' value='Promote'>";
	print "                               <input type=button name='btn_cancel' value='Cancel' onclick='location=\"".$return_url."\";'></td>\r";
	print "</table>\r";
	print "<input type='hidden' name='hdn_function' value='PROMOTE'>\r";
	print "<input type='hidden' name='hdn_file_id' value='".$file_id."'>\r";
	print "<input type='hidden' name='hdn_return_url' value='".$return_url."'>\r";
	print "</form>\r";
	
	include_once XOOPS_ROOT_PATH.'/footer.php';
	}
?>
