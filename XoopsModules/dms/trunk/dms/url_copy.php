<?php
//  ------------------------------------------------------------------------ //
//                     Document Management System                            //
//                  Written By:  Brian E. Reifsnyder                         //
//                        Copyright 2007                                     //
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

// url_copy.php

include '../../mainfile.php';
include_once 'inc_dms_functions.php';

function dms_url_copy($obj_id, $dest_obj_owner)
	{
	global $dms_config,$dmsdb,$dms_user_id;

	// Determine the type of folder the url is being copied into.
	$query  = "SELECT obj_type FROM ".$dmsdb->prefix("dms_objects")." WHERE obj_id='".$dest_obj_owner."'";
	$dest_obj_type = $dmsdb->query($query,"obj_type");
	
	if($dest_obj_type == DISKDIR)
		{
		//  Don't do anything...we can't copy a URL to a disk directory.
		}
	else
		{
		$source_obj_id = $obj_id;
		
		// Get the name of the source url.
		$query  = "SELECT obj_name FROM ".$dmsdb->prefix("dms_objects")." ";
		$query .= "WHERE obj_id='".$obj_id."'";
		$source_object_name = $dmsdb->query($query,"obj_name");
		
		//  Get the URL of the web page
		$query  = "SELECT data FROM ".$dmsdb->prefix("dms_object_misc")." ";
		$query .= "WHERE obj_id='".$obj_id."' AND data_type='".URL."'";
		$source_url = $dmsdb->query($query,"data");

		// Create the new object in dms_objects
		$query  = "INSERT INTO ".$dmsdb->prefix("dms_objects")." (obj_type,obj_name,obj_owner,time_stamp_create) VALUES (";
		$query .= "'".WEBPAGE."',";
		$query .= "'".$source_object_name."',";
		$query .= "'".$dest_obj_owner."',";
		$query .= "'".time()."')";
		$dmsdb->query($query);

		// Get the obj_id of the new object
		$obj_id = $dmsdb->getid();
		
		//  Create an entry in dms_object_properties.
		$query  = "INSERT INTO ".$dmsdb->prefix('dms_object_properties')." (obj_id)";
		$query .= " VALUES ('";
		$query .= $obj_id."')";
		$dmsdb->query($query);

		// Copy all of the document permissions.
		if( ($dms_config['inherit_perms'] == 1) && ($dest_obj_owner > 0) )
			{
			// Use the permissions inherited from the destination folder
			$perms_source = $dest_obj_owner;
			}
		else
			{
			// Use the permissions copied from the original document
			$perms_source = $source_obj_id;
			}
		
		$query = "SELECT * from ".$dmsdb->prefix('dms_object_perms')." WHERE ptr_obj_id='".$source_obj_id."'";
		$result = $dmsdb->query($query);
		$num_rows = $dmsdb->getnumrows();
		
		if($num_rows > 0)
			{
			while($result_data = $dmsdb->getarray($result))
				{
				$query  = "INSERT INTO ".$dmsdb->prefix('dms_object_perms')." ";
				$query .= "(ptr_obj_id,user_id, group_id, user_perms, group_perms, everyone_perms) VALUES ('";
				$query .= $obj_id."','";
				$query .= $result_data['user_id']."','";
				$query .= $result_data['group_id']."','";
				$query .= $result_data['user_perms']."','";
				$query .= $result_data['group_perms']."','";
				$query .= $result_data['everyone_perms']."')";

				$dmsdb->query($query);
				}
			}
	
		// Find the row_id of the entry just created in dms_object_versions.
		$dms_object_versions_row_id = $dmsdb->getid();
		
		// Enter the row_id of the entry for the current version into dms_objects
		$query  = "UPDATE ".$dmsdb->prefix('dms_objects');
		$query .= " SET current_version_row_id='".$dms_object_versions_row_id."' ";
		$query .= " WHERE obj_id='".$obj_id."'";  
		
		$dmsdb->query($query);

		// Store URL in dms_object_misc
		$query  = "INSERT INTO ".$dmsdb->prefix('dms_object_misc')." ";
		$query .= "(obj_id,data_type,data) VALUES ('";
		$query .= $obj_id."','";
		$query .= URL."','";
		$query .= $source_url."')";
		
		$dmsdb->query($query);
		}
		
	return $obj_id;
	}

if (dms_get_var("hdn_url_copy") == "confirm")
	{
	$dest_obj_owner = dms_get_var("rad_folder_id");
	
	$obj_id = dms_get_var("hdn_obj_id");

	$location = "url_options.php?obj_id=".$obj_id;
	
	$new_obj_id = dms_url_copy($obj_id,$dest_obj_owner);

	//dms_document_name_sync($new_obj_id);
	
	dms_auditing($obj_id,"url/copy/dest obj id=".$obj_id."/dest folder id=".$dest_obj_owner);
	dms_auditing($new_obj_id,"url/copy/source obj id=".$obj_id);
	
	dms_folder_subscriptions($new_obj_id);
	
	dms_message("The web page has been copied to the selected destination directory.");
	
	//header("Location:".$location);
	dms_header_redirect($location);
	
	exit(0);
	}
else
	{
	$obj_id = dms_get_var("hdn_obj_id");
	if($obj_id == FALSE) $obj_id = dms_get_var("obj_id");
	//if ($HTTP_POST_VARS["hdn_obj_id"]) $obj_id = $HTTP_POST_VARS['hdn_obj_id'];
	//else $obj_id = $HTTP_GET_VARS['obj_id']; 
	
	// Permissions required to access this page:
	//  EDIT, OWNER
	$perms_level = dms_perms_level($obj_id);
	
	if ( ($perms_level != 3) && ($perms_level != 4) )
	{
	print("<SCRIPT LANGUAGE='Javascript'>\r");
	print("location='index.php';");
	print("</SCRIPT>");  
	end();
	}
	
	$location = "file_copy.php";
		
	include 'inc_pal_header.php';
	
	// Get file information
	$query  = "SELECT obj_name from ".$dmsdb->prefix("dms_objects")." ";
	$query .= "WHERE obj_id='".$obj_id."'";  
	$doc_name = $dmsdb->query($query,'obj_name');
	
	print "  <table width='100%'>\r";
	print "  <form method='post' name='frm_select_dest' action='url_copy.php'>\r";
	display_dms_header(2);
	
	print "  <tr><td colspan='2'><BR></td></tr>\r";
	print "  <tr><td colspan='2' align='left'><b>Copy Web Page:</b></td></tr>\r";
	print "  <tr><td colspan='2'><BR></td></tr>\r";
	print "  <tr>\r";
	print "    <td colspan='2' align='left'>Name:&nbsp;&nbsp;&nbsp;";
	print "        ".$doc_name."</td>\r";
	print "  </tr>\r";
	print "  <tr><td colspan='2'><BR></td></tr>\r";
	
	print "  <tr>\r";
	print "    <td colspan='2' align='left'>\r";
	
	include "inc_folder_select.php";
	
	print "    </td>\r";
	print "  </tr>\r";
	
	print "  <tr><td colspan='2'><BR></td></tr>\r";
	
	print "  <td colspan='2' align='left'><input type=button name='btn_submit' value='Copy' onclick='check_for_dest();'>";
	print "                               <input type=button name='btn_cancel' value='Cancel' onclick='location=\"url_options.php?obj_id=".$obj_id."\";'></td>\r";
	print "</table>\r";
	print "<input type='hidden' name='hdn_url_copy' value='confirm'>\r";
	print "<input type='hidden' name='hdn_obj_id' value='".$obj_id."'>\r";
	print "<input type='hidden' name='hdn_destination_folder_id' value=''>\r";
	print "</form>\r";
	
	include_once 'inc_pal_footer.php';
	}
?>



