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

// file_new.php

include '../../mainfile.php';
include_once 'inc_dms_functions.php';
include_once 'inc_dest_path_and_file.php';
include_once 'inc_file_properties.php';
include_once 'inc_adn_system.php';
include_once 'inc_adv_system.php';

if (dms_get_var("hdn_file_new") == "confirm")
	{
	$location = "file_options.php?obj_id=";
	$time_stamp = time();
	$partial_path_and_file = dest_path_and_file();

	// Get the location of the document repository
	$file_sys_root = $dms_config['doc_path'];

	$dest_path_and_file    = $file_sys_root."/".$partial_path_and_file;

	// Get the name, path and file of the source file.
	$query  = "SELECT obj_name, current_version_row_id FROM ".$dmsdb->prefix("dms_objects")." ";
	$query .= "WHERE obj_id='".dms_get_var("rad_file_id")."'";
	$source_object = $dmsdb->query($query,'ROW');

	$query  = "SELECT file_path, file_name, file_type, file_size FROM ".$dmsdb->prefix("dms_object_versions")." ";
	$query .= "WHERE row_id='".$source_object->current_version_row_id."'";
	$source_file_info = $dmsdb->query($query,'ROW');

	$source_path_and_file = $dms_config['doc_path']."/".$source_file_info->file_path;

	// Get active folder
	$query = "SELECT folder_id from ".$dmsdb->prefix("dms_active_folder")." WHERE user_id='".$dms_user_id."'";  
	$active_folder = $dmsdb->query($query,'folder_id');

	if(!$active_folder>=1) $active_folder=0;

	if($dms_admin_flag == 0)
		{  
		$active_folder_perms = dms_perms_level($active_folder);
		if( ($active_folder_perms != EDIT) && ($active_folder_perms != OWNER) ) 
			{
			print "<SCRIPT LANGUAGE='Javascript'>\r";
			print "location='index.php';";
			print "</SCRIPT>";  
			}
		}

	// Copy the file.
	if (!copy($source_path_and_file,$dest_path_and_file))
		{
		print "Error:  Failure to copy file.  Either source path or destination path is not accessable.\r";
		exit(0);
		}

	// Set the $obj_name and the $file_name.  If the user has not entered a document extension, add one.
	$obj_name = dms_strprep(dms_get_var("txt_obj_name"));
	if(0 == strlen($obj_name)) $obj_name = $source_file_info->file_name;
	$file_name = $obj_name;
	$file_name = str_replace(" ","_",$file_name);    // Replace <space> with _
	// If an extension has not been given for the document, add one. 
	if(!strrchr($file_name,".")) 
	  $file_name = dms_filename_plus_ext($file_name,$source_file_info->file_type);
	
	$file_name = dms_strprep($file_name);
	  
	// Create the new object in dms_objects
	$query  = "INSERT INTO ".$dmsdb->prefix('dms_objects')." ";
	$query .= "(template_obj_id,obj_type,obj_name,obj_status,obj_owner,obj_checked_out_user_id,time_stamp_create,file_type) ";
	$query .= "VALUES ('";
	$query .= dms_get_var("rad_file_id")."','";
	$query .= "0','";
	$query .= $obj_name."','";
	$query .= "1','";
	$query .= $active_folder."','";
	$query .= $dms_user_id."','";
	$query .= $time_stamp."','";
	$query .= $source_file_info->file_type."')";

	$dmsdb->query($query);

	// Get the obj_id of the new object
	$obj_id = $dmsdb->getid();

	dms_perms_set_init($obj_id,$active_folder);

	// Create an entry in dms_object_properties.
	$query  = "INSERT INTO ".$dmsdb->prefix('dms_object_properties')." (obj_id)";
	$query .= " VALUES ('";
	$query .= $obj_id."')";

	//mysql_query($query);
	$dmsdb->query($query);

	// Add all additional document properties
	//update_file_properties($obj_id);

	$file_type = $source_file_info->file_type;
	//if($dms_config['OS']=="Linux") $file_type = trim(exec('file -bi '. escapeshellarg($dest_path_and_file)));
	
	
	// Create an entry in dms_object_versions and store the appropriate information.
	$query  = "INSERT INTO ".$dmsdb->prefix('dms_object_versions')." (obj_id,file_path,file_name,file_type,file_size,";
	$query .= "major_version,minor_version,sub_minor_version,init_version_flag,time_stamp)";
	$query .= " VALUES ('";
	$query .= $obj_id."','";
	$query .= $partial_path_and_file."','";
	//$query .= $source_file_info->file_name."','";
	$query .= $file_name."','";
	$query .= $file_type."','";
	$query .= $source_file_info->file_size."','";
	$query .= "1"."','";
	$query .= "0"."','";
	$query .= "0"."','";
	$query .= "1"."','";
	$query .= $time_stamp."')";

	$dmsdb->query($query);
	//mysql_query($query);  

	// Find the row_id of the entry just created in dms_object_versions.
	//$dms_object_versions_row_id = mysql_insert_id();
	$dms_object_versions_row_id = $dmsdb->getid();

	// Enter the row_id of the entry for the current version into dms_objects
	$query  = "UPDATE ".$dmsdb->prefix('dms_objects');
	$query .= " SET current_version_row_id='".$dms_object_versions_row_id."' ";
	$query .= " WHERE obj_id='".$obj_id."'";  

	$dmsdb->query($query);
	//mysql_query($query);

	$location .= $obj_id;

	 // Update the misc_text in the dms_objects table
	dms_update_misc_text($obj_id);
	
	dms_auditing($obj_id,"document/new");

	// Add the document number to the document.  If the ADN system is not enabled, the function will return without making changes.
	dms_adn_system($obj_id);
	
	// Add the document version number to the document.
	dms_adv_system($obj_id);
	
	dms_folder_subscriptions($obj_id);
	
	dms_document_name_sync($obj_id);
	
	dms_doc_history($obj_id);
	
	print "<SCRIPT LANGUAGE='Javascript'>\r";
	// Open the document, in the appropriate application, for editing.
	print "tempwindow = window.open('file_retrieve.php?function=open&obj_id=".$obj_id."','');\r";
	print "location='".$location."';";
	print "</SCRIPT>";  

	// Don't use the header() function because of opening a new window with the document.  
	//header("Location:".$location);
	}
else
	{
	include 'inc_pal_header.php';
	
	$location="file_new.php";  
	
	// Get active folder
	/*
	$query = "SELECT folder_id from ".$xoopsDB->prefix("dms_active_folder")." WHERE user_id='".$dms_user_id."'";  
	$result = mysql_query($query);
	$active_folder = mysql_result($result,'folder_id');
	if(!$active_folder>=1) $active_folder=0;
	*/
	$active_folder = dms_active_folder();
	
	
	if($dms_admin_flag == 0)
			{  
			$active_folder_perms = dms_perms_level($active_folder);
			if( ($active_folder_perms != EDIT) && ($active_folder_perms != OWNER) ) 
				{
				print("<SCRIPT LANGUAGE='Javascript'>\r");
				print("location='index.php';");
				print("</SCRIPT>");  
				}
			}  
	
	//$file_id=$HTTP_GET_VARS["file_id"];
	
	print "  <table width='100%'>\r";
	print "  <form method='post' name='frm_select_template' action='file_new.php'>\r";
	display_dms_header(2);
	
	print "  <tr><td colspan='2'><BR></td></tr>\r";
	print "  <tr><td colspan='2' align='left'><b>Create Document:</b></td></tr>\r";
	print "  <tr><td colspan='2'><BR></td></tr>\r";
	
	print "  <tr>\r";
	print "    <td colspan='2' align='left'>\r";
	
	$query  = "SELECT data FROM ".$dmsdb->prefix('dms_config')." ";
	$query .= "WHERE name='template_root_obj_id'";
	$root_folder = $dmsdb->query($query,'data');
	
	print "Select Template:\r&nbsp;&nbsp;&nbsp;";
	
	include "inc_file_select.php";
	
	print "    </td>\r";
	print "  </tr>\r";
	
	print "  <tr><td colspan='2'><br></td></tr>\r";
	
	print "  <tr>\r";
	print "    <td colspan='2' align='left' class='".$dms_config['class_content']."'>\r";  
	print "      "._DMS_FILE_NAME."  ";
	print "      <input type='text' name='txt_obj_name' size='40' maxlength='250' class='".$dms_config['class_content']."' tabindex='".$dms_tab_index++."'>\r";
	print "    </td>\r";
	print "  </tr>\r";
	
	print "  <tr><td colspan='2'><BR></td></tr>\r";
		
	print "  <td colspan='2' align='left'><input type='submit' name='btn_submit' value='Create' tabindex='".$dms_tab_index++."'>";
	print "                               <input type='button' name='btn_cancel' value='Cancel' onclick='location=\"index.php\";' tabindex='".$dms_tab_index++."'></td>\r";
	print "</table>\r";
	print "<input type='hidden' name='hdn_file_new' value='confirm'>\r";
	print "</form>\r";
	
	print("<SCRIPT LANGUAGE='Javascript'>\r");
	print("  document.frm_select_template.txt_obj_name.focus();");
	print("</SCRIPT>");
	
	include_once 'inc_pal_footer.php';
	}
?>



