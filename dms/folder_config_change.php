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

// folder_type_change.php

include '../../mainfile.php';
include 'inc_dms_functions.php';

//print "oi: ".$HTTP_POST_VARS['hdn_obj_id']."<BR>";
//print "ot: ".$HTTP_POST_VARS['hdn_obj_type']."<br>";
//print "fd: ".$HTTP_POST_VARS['hdn_filesys_dir']."<br>";
//exit(0);

if (dms_get_var("hdn_obj_id") != FALSE)
	{
	$obj_id = dms_get_var("hdn_obj_id");
	
	// Delete the filesystem path
	$query  = "DELETE FROM ".$dmsdb->prefix('dms_object_misc')." WHERE ";
	$query .= "obj_id='".$obj_id."' AND ";
	$query .= "data_type='".PATH."'"; 
	$dmsdb->query($query);
	
	// Set the folder to the new type
	$query  = "UPDATE ".$dmsdb->prefix('dms_objects')." SET obj_type='".dms_get_var("slct_folder_type")."' ";
	$query .= "WHERE obj_id='".$obj_id."'";
	$dmsdb->query($query);

	if(dms_get_var("slct_folder_type") == DISKDIR)
		{
		$query  = "INSERT INTO ".$dmsdb->prefix('dms_object_misc')." (obj_id,data_type,data) VALUES ";
		$query .= "('".$obj_id."','".PATH."','".dms_get_var("txt_directory")."')";
		$dmsdb->query($query);
		}
	
	// Delete the auto lifecycle number
	$query  = "DELETE FROM ".$dmsdb->prefix('dms_object_misc')." WHERE ";
	$query .= "obj_id='".$obj_id."' AND ";
	$query .= "data_type='".FOLDER_AUTO_LIFECYCLE_NUM."'"; 
//print $query;exit(0);
	$dmsdb->query($query);
	
	if(dms_get_var("txt_folder_auto_lifecycle_num") > 0)
		{
		$query  = "INSERT INTO ".$dmsdb->prefix('dms_object_misc')." (obj_id,data_type,data) VALUES ";
		$query .= "('".$obj_id."','".FOLDER_AUTO_LIFECYCLE_NUM."','".dms_get_var("txt_folder_auto_lifecycle_num")."')";
		$dmsdb->query($query);
		}
	
	// Set the flags, if applicable.
	
	// Delete the folder archive flag
	$query  = "DELETE FROM ".$dmsdb->prefix('dms_object_misc')." WHERE ";
	$query .= "obj_id='".$obj_id."' AND ";
	$query .= "data_type='".FLAGS."'"; 
	$dmsdb->query($query);

	// If applicable, set the folder archive flag
	$flags = 0;
	if(dms_get_var("chk_folder_archive_flag") == 'on') $flags += 1;
	if(dms_get_var("chk_doc_name_sync_flag") == 'on') $flags += 2;
		
	if($flags > 0)
		{
		$query  = "INSERT INTO ".$dmsdb->prefix('dms_object_misc')." (obj_id,data_type,data) VALUES ";
		$query .= "('".$obj_id."','".FLAGS."','".$flags."')";
		$dmsdb->query($query);
		}

		
	//print "<SCRIPT LANGUAGE='Javascript'>\r";
	//header("Location:folder_options.php?obj_id=".$obj_id);
	
	dms_header_redirect("folder_options.php?obj_id=".$obj_id);
	//print "</SCRIPT>";  
	}

  
