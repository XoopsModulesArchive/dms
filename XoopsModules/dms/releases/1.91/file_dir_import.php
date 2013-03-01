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

// file_dir_import.php

/*
Process to import a file from a file system:

1.  Get the destination location
2.  Move the file into XDMS
3.  Add the object information, permissions, etc.
*/


include '../../mainfile.php';
include_once 'inc_dms_functions.php';

include_once 'inc_dest_path_and_file.php';
include_once 'inc_file_properties.php';

// Get and save the obj_id and obj_num in the variable cache.
if(!isset($dms_var_cache['fdi_obj_id']))  
	{
	$dms_var_cache['fdi_obj_id'] = dms_get_var("obj_id");
	}

if(dms_get_var("obj_id") != FALSE) $dms_var_cache['fdi_obj_id'] = dms_get_var("obj_id");

// Save the obj_id and obj_num in case the user expands or contracts a folder.
if(!isset($dms_var_cache['fdi_obj_num']))  
	{
	$dms_var_cache['fdi_obj_num'] = dms_get_var("obj_num");
	}

if(dms_get_var("obj_num") != FALSE) $dms_var_cache['fdi_obj_num'] = dms_get_var("obj_num");

dms_var_cache_save();

$obj_id = $dms_var_cache['fdi_obj_id'];
$obj_num = $dms_var_cache['fdi_obj_num'];

if (dms_get_var("hdn_file_dir_import") == "confirm")
	{
	$location = "file_options.php?obj_id=";    // obj_id will need to be added after it is determined

	// Get the filename and path that is refered to by $obj_num and place in $source_path_and_file.
	$source_path_and_file = "";
	
	$query  = "SELECT data from ".$dmsdb->prefix("dms_object_misc")." ";
	$query .= "WHERE obj_id=".$obj_id." AND data_type='".PATH."'";
	$dir = $dmsdb->query($query,'data');
	
	$file_list = array();
	
	$counter = 0;
	$handle = opendir($dir);
	while( ($file = readdir($handle) ) != false)
		{
		if($file =='.' || $file =='..') continue;
			
		if("file" == filetype($dir."/".$file))
			{
			$file_list[$counter] = $file."\n";
			$counter++;
			}
		}
	
	closedir($handle);
	
	sort($file_list);
	
	$counter = 0;
	while($file_list[$counter])
		{
		if($counter == $obj_num) 
			{
			$source_file_name = $file_list[$counter];
			$source_path_and_file = $dir."/".$file_list[$counter];
			}
		$counter++;
		}

	// Error trapping:  If there isn't a $source_path_and_file found, exit with an error.
	if(strlen($source_path_and_file) < 1)
		{
		print "Error:  Source file not found in source directory.\r";
		exit(0);
		}

	$source_file_name = trim($source_file_name);
	$source_path_and_file = trim($source_path_and_file);
		
	$time_stamp = time();

	// Determine the location of the file starting from the root of the repository.
	$partial_path_and_file = dest_path_and_file();

	// Get the location of the document repository
	$file_sys_root = $dms_config['doc_path'];

	// Determine the destination path and file 
	$dest_path_and_file = $file_sys_root."/".$partial_path_and_file;

	// Before anything else is done, get the type and size of the file
	$file_type = "";  //$file_type = mime_content_type($source_path_and_file);
	$file_size = filesize($source_path_and_file);
	
	// Copy the source file to the repository.
	
	if(!copy($source_path_and_file,$dest_path_and_file))
		{
		print "Error:  Unable to import file.<BR>SP:".$source_path_and_file."<BR>DP:".$dest_path_and_file;
		exit(0);
		}


	// Delete the source file
	unlink($source_path_and_file);

	// Create the name of the object;
	$obj_name =  $source_file_name;
	$obj_name = dms_strprep($obj_name);

	// Create the new object in dms_objects
	$query  = "INSERT INTO ".$dmsdb->prefix('dms_objects')." (obj_type,obj_name,obj_owner,time_stamp_create)";
	$query .= " VALUES ('";
	$query .= "0','";
	$query .= $obj_name."','";
	$query .= $HTTP_POST_VARS['rad_folder_id']."','";
	$query .= $time_stamp."')";

	//print $query;  
	//print "<BR>";    
	$dmsdb->query($query);

	// Get the obj_id of the new object.  From this point on, $obj_id is the database object id NOT the source folder id.
	$obj_id = $dmsdb->getid();

	// Store the owner permissions in dms_object_perms  TEMP CODE
	$query  = "INSERT INTO ".$dmsdb->prefix('dms_object_perms')." ";
	$query .= "(ptr_obj_id,user_id, group_id, user_perms, group_perms, everyone_perms) VALUES ('";
	$query .= $obj_id."','";
	$query .= $dms_user_id."','";
	$query .= "0','";
	$query .= "4','";
	$query .= "0','";
	$query .= "0')";

	$dmsdb->query($query);

	// Create an entry in dms_object_properties.
	$query  = "INSERT INTO ".$dmsdb->prefix('dms_object_properties')." (obj_id)";
	$query .= " VALUES ('";
	$query .= $obj_id."')";

	$dmsdb->query($query);

	// Add all additional document properties
	update_file_properties($obj_id);

	// Create an entry in dms_object_versions and store the appropriate information.
	$query  = "INSERT INTO ".$dmsdb->prefix('dms_object_versions')." (obj_id,file_path,file_name,file_type,file_size,";
	$query .= "major_version,minor_version,sub_minor_version,time_stamp)";
	$query .= " VALUES ('";
	$query .= $obj_id."','";
	$query .= $partial_path_and_file."','";
	$query .= $source_file_name."','";
	$query .= $file_type."','";
	$query .= $file_size."','";
	$query .= "1','";
	$query .= "0','";
	$query .= "0','";
	$query .= $time_stamp."')";

	//print $query;  
	$dmsdb->query($query);  

	// Find the row_id of the entry just created in dms_object_versions.
	$dms_object_versions_row_id = $dmsdb->getid();

	// Enter the row_id of the entry for the current version into dms_objects
	$query  = "UPDATE ".$dmsdb->prefix('dms_objects');
	$query .= " SET current_version_row_id='".$dms_object_versions_row_id."' ";
	$query .= " WHERE obj_id='".$obj_id."'";  

	$dmsdb->query($query);

	dms_auditing($obj_id,"document/dir import");

	$location .= $obj_id;
	
	//header("Location:".$location);
	
	dms_header_redirect($location);
	}
else
	{
	include XOOPS_ROOT_PATH.'/header.php';

	/*  
	if ($HTTP_POST_VARS["hdn_obj_id"]) $obj_id = $HTTP_POST_VARS['hdn_obj_id'];
	else $obj_id = $HTTP_GET_VARS['obj_id'];
	*/

	// Permissions required to access this page:
	//  BROWSE, READONLY, EDIT, OWNER
	$perms_level = dms_perms_level($obj_id);

	if ( ($perms_level != 1) && ($perms_level != 2) && ($perms_level != 3) && ($perms_level != 4) )
		{
		print("<SCRIPT LANGUAGE='Javascript'>\r");
		print("location='index.php';");
		print("</SCRIPT>");  
		end();
		}

	$location="file_dir_import.php";

	print "  <table width='100%'>\r";
	print "  <form method='post' name='frm_select_dest' action='file_dir_import.php'>\r";
	display_dms_header(2);

	print "  <tr><td colspan='2'><BR></td></tr>\r";
	print "  <tr><td colspan='2' align='left'><b>Import File</b></td></tr>\r";
	print "  <tr><td colspan='2'><BR></td></tr>\r";
	print "  <tr><td colspan='2' align='left'>Select Destination Folder:</td></tr>\r";
	print "  <tr><td colspan='2'><BR></td></tr>\r";

	print "  <tr>\r";
	print "    <td colspan='2' align='left'>\r";

	include "inc_folder_select.php";

	print "    </td>\r";
	print "  </tr>\r";

	print "  <tr><td colspan='2'><BR></td></tr>\r";

	print "  <td colspan='2' align='left'><input type=button name='btn_submit' value='Import' onclick='check_for_dest();'>";
	print "                               <input type=button name='btn_cancel' value='" . _DMS_CANCEL . "' onclick='location=\"index.php\";'></td>\r";
	print "</table>\r";
	print "<input type='hidden' name='hdn_file_dir_import' value='confim'>\r";
	print "</form>\r";

	include_once XOOPS_ROOT_PATH.'/footer.php';
	}
?>



