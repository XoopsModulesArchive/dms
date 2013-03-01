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

// file_batch_import.php

include '../../mainfile.php';
include_once 'inc_dest_path_and_file.php';
include_once 'inc_dms_functions.php';
include_once 'inc_file_properties.php';
include_once 'inc_adn_system.php';
include_once 'inc_adv_system.php';
include_once 'inc_file_copy.php';
include_once 'inc_lifecycle_functions.php';
include_once 'inc_file_upload.php';


if(dms_get_var('hdn_temp_file_name') != FALSE)
	{
	$temp_file_name = dms_get_var('hdn_temp_file_name');

	// Check for an upload error.
	$error_code = $_FILES[$temp_file_name]['error'];
	if($error_code > 0)
		{
		$error_message = dms_get_file_upload_error_message($error_code);
	
		include XOOPS_ROOT_PATH.'/header.php';
		print "<table>\r";
//		print "  <tr><td align='left'>Error:  The document has not been imported into the system.  The most likely problem is that the document is too large.  The current maximum supported document size is ".$upload_max_filesize." bytes.</td></tr>\r";
		print "  <tr><td align='left'>Error:  The document has not been imported into the system.</td></tr>\r";
		print "  <tr><td align='left'>".$error_message."</td></tr>\r";
		print "  <tr><td><BR></td></tr>\r";
		print "  <tr><td align='left'><input type='button' name='btn_continue' value='Continue' onclick='location=\"file_import.php\";'></td></tr>\r";
		
		include_once XOOPS_ROOT_PATH.'/footer.php';
		exit(0);

		}
		
	$unzip_path = XOOPS_ROOT_PATH."/modules/dms/temp/".$dms_user_id;
	$unzip_path_and_file = XOOPS_ROOT_PATH."/modules/dms/temp/".$dms_user_id."/batch.zip";
		
	$time_stamp = time();
	$active_folder = dms_active_folder();
	
	// Create a temporary directory that is the name of the user id.
	mkdir($unzip_path,0775);
	chmod($unzip_path,0777);
	
	// Get the temporary path and file of the recent upload.
	$source_path_and_file  = $_FILES[$temp_file_name]["tmp_name"];  //$_FILES[dms_get_var("hdn_temp_file_name")]["tmp_name"];
	
	if(is_uploaded_file($source_path_and_file))
		{
		move_uploaded_file($source_path_and_file,$unzip_path_and_file) or die("Error:  Unable to move document.  Ensure that the temporary directory is available and that the web server is able to access the temporary directory.<BR>SP:".$source_path_and_file."<BR>DP:".$unzip_path_and_file);
		}
	else 
		die("Error:  Uploaded document is not unavailable.<BR>SP:".$source_path_and_file."<BR>DP:".$unzip_path_and_file);

	// unzip the documents into the temporary directory
	$command = "unzip -qq -j ".$unzip_path_and_file." -d ".$unzip_path;
	exec($command);

	// delete batch.zip
	$command = "rm ".$unzip_path_and_file;
	exec($command);
	
	//  get the file names of all documents in the temporary directory
	//$file_list = array();
	
	$handle = opendir($unzip_path);
	while( ($file = readdir($handle) ) != false)
		{
		if($file =='.' || $file =='..') continue;

		if("file" == filetype($unzip_path."/".$file))
			{
			$obj_name =  $file."\n";
			$obj_name = dms_strprep($obj_name);
			$source_file_name = $file; //."\n";
			
			$source_path_and_file = $unzip_path."/".$source_file_name;
			
			// Determine the location of the file starting from the root of the repository.
			$partial_path_and_file = dest_path_and_file();
		
			// Get the location of the document repository
			$file_sys_root = $dms_config['doc_path'];
		
			// Determine the destination path and file 
			$dest_path_and_file = $file_sys_root."/".$partial_path_and_file;
		
			// Before anything else is done, get the type and size of the file
			$file_type = ""; //mime_content_type($source_path_and_file);
			if($dms_config['os']=="Linux") $file_type = trim(exec('file -bi '. escapeshellarg($source_path_and_file)));
			
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
		//	$obj_name = dms_strprep($obj_name);
		
			// Create the new object in dms_objects
			$query  = "INSERT INTO ".$dmsdb->prefix('dms_objects')." (obj_type,obj_name,obj_owner,time_stamp_create)";
			$query .= " VALUES ('";
			$query .= "0','";
			$query .= $obj_name."','";
			$query .= $active_folder."','";
			$query .= $time_stamp."')";
			
			$dmsdb->query($query);
		
			// Get the obj_id of the new object.  From this point on, $obj_id is the database object id NOT the source folder id.
			$obj_id = $dmsdb->getid();
		
			// Store the owner permissions in dms_object_perms  
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
		
			dms_auditing($obj_id,"document/batch import");
			
			// Add the document number to the document.  If the ADN system is not enabled, the function will return without making changes.
			dms_adn_system($obj_id);
			
			// Add the version number to the document.  If the ADV system is not enabled, the function will return without making changes.
			dms_adv_system($obj_id);
			
			dms_folder_subscriptions($obj_id);
		
			// Synchronize the Document name and the File Name
			dms_document_name_sync($obj_id);
		
			// Check to see if an automatic lifecycle exists for this document.  If it exists, apply it.
			$query  = "SELECT data FROM ".$dmsdb->prefix("dms_object_misc")." ";
			$query .= "WHERE obj_id=".$active_folder." AND data_type='".FOLDER_AUTO_LIFECYCLE_NUM."'";
			$folder_auto_lifecycle_num = $dmsdb->query($query,'data');
		
			if($dmsdb->getnumrows() == 1) dms_apply_lifecycle($obj_id,$folder_auto_lifecycle_num);
			}
		}
	rmdir($unzip_path);
		
	closedir($handle);
	
	dms_message("The documents have been imported into the document management system.");

	dms_header_redirect("index.php");
	
	}
else
	{
	include XOOPS_ROOT_PATH.'/header.php';
	
	print "<script language='JavaScript'>\r";
	//print "<!--\r";
	
	print "function paste_doc_name()\r";
	print "  {\r";
	print "  var file_name,start_position,path;\r";
	print "  if(document.frm_file_import.txt_file_name.length > 0) return(0);\r";
	print "  path = document.frm_file_import.".$temp_file_name.".value;\r";
	print "  start_position = path.lastIndexOf(\"\\\\\");\r";
	print "  if(start_position == -1) start_position = path.lastIndexOf(\"/\");\r";
	print "  start_position++;\r";
	print "  file_name = path.substring(start_position,path.length);\r";
	print "  document.frm_file_import.txt_file_name.value = file_name;\r";
	print "  //document.frm_file_import.txt_file_name.length = file_name.length;\r";
	print "  }\r";
	
	print "function set_doc_length()\r";
	print "  {\r";
	print "  var file_name = document.frm_file_import.txt_file_name.value;\r";
	print "  document.frm_file_import.txt_file_name.length = file_name.length;\r";
	print "  }\r";
	
	//print "// -->\r";
	print "</script>\r";
	
	// Get active folder
	$active_folder = dms_active_folder();
		
	if(!$dms_admin_flag)
		{  
		$active_folder_perms = dms_perms_level($active_folder);
		if( ($active_folder_perms != EDIT) && ($active_folder_perms != OWNER) ) 
			{
			print("<SCRIPT LANGUAGE='Javascript'>\r");
			print("location='index.php';");
			print("</SCRIPT>");  
			}
		}
		
	print "<form name='frm_file_import' method='post' action='file_batch_import.php' enctype='multipart/form-data'>\r";
	print "<table width='100%'>\r";
	
	display_dms_header();
	print "  <tr><td colspan='2' align='left'><BR></td></tr>\r";
	print "  <tr><td colspan='2' align='left'><b>Import Multiple Documents:</b></td></tr>\r";
	print "  <tr><td colspan='2' align='left'><BR></td></tr>\r";
	print "  <tr>\r";
	
	print "  <tr>\r";
	print "    <td align='left'>Select Zip File:</td>";
	print "      <input type='hidden' name='MAX_FILE_SIZE' value='".$upload_max_filesize."'>\r";
	print "    <td align='left'><input name='".$temp_file_name."' size='30' type='file' tabindex='".$dms_tab_index++."' onchange='paste_doc_name();'></td>\r";
	print "  </tr>\r";
	print "  <tr><td colspan='2'><BR></td></tr>\r";
	print "  <td colspan='2' align='left'><input type=submit name='btn_submit' value='Submit' tabindex='".$dms_tab_index++."'>";
	print "    <input type=button name='btn_cancel' value='Cancel' onclick='location=\"index.php\";' tabindex=".$dms_tab_index++."></td>\r";
	print "</table>\r";
	print "<input type='hidden' name='hdn_temp_file_name' value='".$temp_file_name."'>\r";
	//print "<input type='hidden' name='hdn_active_folder' value='".$active_folder."'>\r";
	print "</form>\r";
	
	print("<SCRIPT LANGUAGE='Javascript'>\r");
	print("  document.frm_file_import.txt_file_name.focus();");
	print("</SCRIPT>");  
	
	include_once XOOPS_ROOT_PATH.'/footer.php';
  }
?>
